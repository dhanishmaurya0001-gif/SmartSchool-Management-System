<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Optional progress-card renderer. Existing marksheet templates keep their own renderer. */
class Photo_progress_card
{
    private $ci;
    public function __construct() { $this->ci =& get_instance(); }

    public function config($template_id)
    {
        if (!$this->ci->db->table_exists('oss_progress_card_templates')) { return null; }
        $row = $this->ci->db->get_where('oss_progress_card_templates', array('template_id'=>(int)$template_id))->row();
        return $row ? json_decode($row->settings, true) : null;
    }

    public function report($config, $student_id)
    {
        $db=$this->ci->db;
        $student=$db->query('SELECT s.*,ss.id AS ssid,ss.class_id,ss.section_id,c.class,se.section,ses.session FROM students s JOIN student_session ss ON ss.student_id=s.id JOIN classes c ON c.id=ss.class_id JOIN sections se ON se.id=ss.section_id JOIN sessions ses ON ses.id=ss.session_id WHERE s.id=? AND ss.session_id=?',array((int)$student_id,(int)$config['session_id']))->row_array();
        if (!$student) { show_error('Student is not enrolled in the progress-card session.',404); }
        $ids=array_values($config['exams']);
        $placeholders=implode(',',array_fill(0,count($ids),'?'));
        $params=array_merge(array((int)$student_id),$ids);
        $rows=$db->query('SELECT sub.id AS subject_id,sub.name,es.exam_group_class_batch_exams_id AS exam_id,es.max_marks,r.get_marks,r.attendence FROM exam_group_class_batch_exam_students st JOIN exam_group_class_batch_exam_subjects es ON es.exam_group_class_batch_exams_id=st.exam_group_class_batch_exam_id JOIN subjects sub ON sub.id=es.subject_id LEFT JOIN exam_group_exam_results r ON r.exam_group_class_batch_exam_student_id=st.id AND r.exam_group_class_batch_exam_subject_id=es.id WHERE st.student_id=? AND es.exam_group_class_batch_exams_id IN ('.$placeholders.') ORDER BY sub.id', $params)->result_array();
        $subjects=array(); $keyById=array_flip($config['exams']);
        foreach($rows as $row){
            $sid=$row['subject_id'];
            if(!isset($subjects[$sid])) {$subjects[$sid]=array('name'=>$row['name'],'values'=>array());}
            $key=$keyById[$row['exam_id']];
            $subjects[$sid]['values'][$key]=array('mark'=>$row['get_marks']===null ? null : (float)$row['get_marks'],'max'=>(float)$row['max_marks'],'absent'=>$row['attendence']==='absent');
        }
        $totals=array_fill_keys(array('h1','h2','hm','hi','a1','a2','am','ai','hw','aw','written','internal','max'),0);
        $complete=count($subjects)>0; $pass=true;
        foreach($subjects as &$subject){
            $v=$subject['values']; $subject['complete']=true;
            foreach($config['exams'] as $key=>$id){
                if(!isset($v[$key]) || $v[$key]['mark']===null){$complete=false;$subject['complete']=false;}
                else {$totals[$key]+=$v[$key]['mark']; $totals['max']+=$v[$key]['max']; if($v[$key]['absent']){$pass=false;}}
            }
            foreach(array('h','a') as $term){
                $subject[$term.'w']=0; $maximum=0;
                foreach(array('1','2','m') as $part){$item=isset($v[$term.$part])?$v[$term.$part]:array('mark'=>null,'max'=>0);$subject[$term.'w']+=(float)$item['mark'];$maximum+=$item['max'];}
                $totals[$term.'w']+=$subject[$term.'w'];
                if($maximum && $subject[$term.'w']/$maximum*100 < $config['pass_percent']){$pass=false;}
            }
        } unset($subject);
        $totals['written']=$totals['hw']+$totals['aw'];$totals['internal']=$totals['hi']+$totals['ai'];
        $total=$totals['written']+$totals['internal'];
        $percentage=$complete&&$totals['max'] ? round($total/$totals['max']*100,2):null;
        $grade=$percentage===null?'Pending':($percentage>=90?'A+':($percentage>=80?'A':($percentage>=70?'B+':($percentage>=60?'B':($percentage>=50?'C':($percentage>=35?'D':'E'))))));
        $attendance=$db->query('SELECT COUNT(*) AS days,SUM(attendence_type_id IN (1,2,3)) AS attended FROM student_attendences WHERE student_session_id=? AND attendence_type_id<>5',array($student['ssid']))->row_array();
        // Competition rank among this class/section, only when every assessment result exists.
        $expected=count($subjects)*count($ids);
        $rankRows=$db->query('SELECT st.student_id,COUNT(r.id) AS entries,SUM(r.get_marks) AS total FROM exam_group_class_batch_exam_students st JOIN student_session ss ON ss.id=st.student_session_id JOIN exam_group_exam_results r ON r.exam_group_class_batch_exam_student_id=st.id WHERE ss.session_id=? AND ss.class_id=? AND ss.section_id=? AND st.exam_group_class_batch_exam_id IN ('.$placeholders.') GROUP BY st.student_id HAVING entries=?',array_merge(array($config['session_id'],$student['class_id'],$student['section_id']),$ids,array($expected)))->result_array();
        $rank=1;foreach($rankRows as $r){if((float)$r['total']>$total){$rank++;}}
        return array('student'=>$student,'subjects'=>$subjects,'totals'=>$totals,'total'=>$total,'percentage'=>$percentage,'grade'=>$grade,'result'=>$complete?($pass?'Passed':'Failed'):'Pending marks','division'=>$percentage===null?'Pending':(!$pass?'—':($percentage>=60?'First':($percentage>=45?'Second':'Third'))),'rank'=>$complete&&count($rankRows)?$rank.' / '.count($rankRows):'Pending','attendance'=>$attendance['days']?round($attendance['attended']/$attendance['days']*100,1).'%':'Not recorded','config'=>$config);
    }

    public function html($config,$student_id)
    {
        return $this->ci->load->view('admin/marksheet/photo_progress_card',$this->report($config,$student_id),true);
    }

    public function output($config,$student_ids)
    {
        $this->ci->load->library('m_pdf');
        $pdf=$this->ci->m_pdf->load(array('tempDir'=>APPPATH.'tmp','format'=>'A4-L','default_font'=>'dejavusans','margin_left'=>8,'margin_right'=>8,'margin_top'=>8,'margin_bottom'=>8));
        $pdf->SetTitle($config['school_name'].' - Progress Card');
        $pdf->SetAuthor($config['school_name']);
        foreach(array_values(array_unique($student_ids)) as $index=>$id){if($index){$pdf->AddPage();}$pdf->WriteHTML($this->html($config,$id));}
        $pdf->Output('School-Annual-Progress-Card.pdf','I');
    }
}
