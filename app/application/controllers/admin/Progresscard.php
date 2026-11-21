<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Progresscard extends Admin_Controller
{
    public function __construct(){parent::__construct();$this->load->library('photo_progress_card');}
    public function index()
    {
        if(!$this->rbac->hasPrivilege('design_marksheet','can_view')){access_denied();}
        if(!$this->db->table_exists('oss_progress_card_templates')){show_error('Progress card setup has not been imported.',404);}
        $templates=$this->db->get('oss_progress_card_templates')->result();
        $id=(int)$this->input->get('template_id');if(!$id && $templates){$id=(int)$templates[0]->template_id;}
        $config=$this->photo_progress_card->config($id);if(!$config){show_404();}
        $error='';
        if($this->input->method()==='post'){
            if(!$this->rbac->hasPrivilege('design_marksheet','can_edit')){access_denied();}
            $new=$config;$new['school_name']=trim((string)$this->input->post('school_name',true));
            $new['character']=trim((string)$this->input->post('character',true));
            $new['note']=trim((string)$this->input->post('note',true));
            if(!$new['school_name'] || strlen($new['school_name'])>150){$error='Enter a school name (maximum 150 characters).';}
            foreach($config['exams'] as $key=>$old){
                $exam=(int)$this->input->post($key);$record=$this->db->get_where('exam_group_class_batch_exams',array('id'=>$exam,'session_id'=>$config['session_id']))->row();
                $max=$key[1]==='1'?30:($key[1]==='2'?50:($key[1]==='m'?20:10));
                $subjects=$this->db->get_where('exam_group_class_batch_exam_subjects',array('exam_group_class_batch_exams_id'=>$exam))->result();
                if(!$record || !$subjects){$error='Choose valid exams from this session, with subjects configured.';}
                foreach($subjects as $subject){if((float)$subject->max_marks!== (float)$max){$error='Maximum marks must match the printed columns: 30 / 50 / 20 / 10.';}}
                $new['exams'][$key]=$exam;
            }
            if(count(array_unique($new['exams']))!==8){$error='Choose eight separate assessment exams.';}
            if(!$error){$this->db->where('template_id',$id)->update('oss_progress_card_templates',array('settings'=>json_encode($new)));$config=$new;$this->session->set_flashdata('msg','Progress card settings saved.');redirect('admin/progresscard?template_id='.$id);}
        }
        $data=array('config'=>$config,'template_id'=>$id,'error'=>$error,'exams'=>$this->db->get_where('exam_group_class_batch_exams',array('session_id'=>$config['session_id']))->result());
        $this->load->view('layout/header');$this->load->view('admin/marksheet/photo_progress_settings',$data);$this->load->view('layout/footer');
    }
    public function download()
    {
        if(!$this->rbac->hasPrivilege('print_marksheet','can_view')){access_denied();}
        $config=$this->photo_progress_card->config((int)$this->input->get('template_id'));
        if(!$config){show_404();}
        $this->photo_progress_card->output($config,array((int)$this->input->get('student_id')));
    }
}
