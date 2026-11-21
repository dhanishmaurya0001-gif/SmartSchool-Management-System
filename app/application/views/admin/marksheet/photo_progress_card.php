<?php defined('BASEPATH') OR exit('No direct script access allowed');
$e=function($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');};
$num=function($n){return $n===null?'—':rtrim(rtrim(number_format($n,2,'.',''),'0'),'.');};
$name=trim($student['firstname'].' '.$student['middlename'].' '.$student['lastname']);
$summary=array('Grace Marks'=>'0 (not applied)','Division'=>$division,'Passed / Failed'=>$result,'Character'=>$config['character'],'Position in Class'=>$rank,'Attendance %'=>$attendance,'Percentage of Marks'=>$percentage===null?'Pending':$num($percentage).'%','Grade'=>$grade,'Grand Total'=>$num($total).' / '.$num($totals['max']));
?>
<style>
.pc{font-family:dejavusans;color:#17170e;background-color:#ffdf72;padding:12px;border:2px solid #242213;font-size:10px;}
.pc h1{font-size:22px;text-align:center;margin:0 0 5px;font-weight:bold;}
.pc .subtitle{text-align:center;font-weight:bold;font-size:12px;margin:0 0 10px;}
.pc table{border-collapse:collapse;width:100%;}.pc .details td{padding:4px 3px;font-size:11px;}
.pc .grid th,.pc .grid td{border:1px solid #282414;padding:5px 3px;text-align:center;height:22px;}
.pc .grid th{font-size:9px;background-color:#f7cc50;}.pc .grid .subject{text-align:left;white-space:nowrap;}
.pc .other td{padding:6px 5px;text-align:left;font-size:9px;height:22px;}
.pc .foot{font-size:8px;margin-top:7px;}.pc .sign td{padding-top:25px;font-size:10px;}
</style>
<div class="pc">
<h1><?php echo $e($config['school_name']); ?></h1>
<div class="subtitle">PROGRESS CARD FOR YEAR <?php echo $e($student['session']); ?></div>
<table class="details"><tr><td width="58%"><b>Student's Name:</b> <?php echo $e($name); ?></td><td width="24%"><b>Roll No:</b> <?php echo $e($student['roll_no']); ?></td><td><b>Scholar No:</b> <?php echo $e($student['admission_no']); ?></td></tr>
<tr><td><b>Father's Name:</b> <?php echo $e($student['father_name']); ?></td><td colspan="2"><b>Mother's Name:</b> <?php echo $e($student['mother_name']); ?></td></tr>
<tr><td><b>Class:</b> <?php echo $e($student['class']); ?> &nbsp; <b>Section:</b> <?php echo $e($student['section']); ?> &nbsp; <b>Date of Birth:</b> <?php echo $e(date('d/m/Y',strtotime($student['dob']))); ?></td><td colspan="2"><b>Aadhaar No:</b> Not provided</td></tr></table>
<table><tr><td width="79%" valign="top">
<table class="grid"><thead><tr><th rowspan="2" width="22%">SUBJECT</th><th colspan="5">HALF YEARLY</th><th colspan="5">ANNUAL</th><th colspan="2">TOTAL</th></tr>
<tr><?php foreach(array('h','a') as $term){ ?><th>FIRST<br>PAPER<br>/30</th><th>SECOND<br>PAPER<br>/50</th><th>MONTHLY<br>TEST<br>/20</th><th>MARK<br>OBTAINED<br>/100</th><th>INTEGER*<br>/10</th><?php } ?><th>TOTAL<br>MARK<br>/200</th><th>TOTAL<br>INTEGER*<br>/20</th></tr></thead><tbody>
<?php $n=0;foreach($subjects as $subject){$n++;$v=$subject['values']; ?><tr><td class="subject"><?php echo $n.'. '.$e(strtoupper($subject['name'])); ?></td>
<?php foreach(array('h','a') as $term){foreach(array('1','2','m','w','i') as $part){ ?><td><?php if($part==='w'){echo $subject['complete']?$num($subject[$term.'w']):'—';}else{$item=isset($v[$term.$part])?$v[$term.$part]:null;echo !$item?'—':($item['absent']?'AB':$num($item['mark']));} ?></td><?php }} ?>
<td><?php echo $subject['complete']?$num($subject['hw']+$subject['aw']):'—'; ?></td><td><?php echo $subject['complete']?$num($v['hi']['mark']+$v['ai']['mark']):'—'; ?></td></tr><?php } ?>
<?php for($blank=$n+1;$blank<=10;$blank++){ ?><tr><td class="subject"><?php echo $blank; ?>.</td><?php for($k=0;$k<12;$k++){ ?><td>&nbsp;</td><?php } ?></tr><?php } ?>
<tr><th>TOTAL</th><?php foreach(array('h1','h2','hm','hw','hi','a1','a2','am','aw','ai','written','internal') as $key){ ?><th><?php echo $num($totals[$key]); ?></th><?php } ?></tr>
</tbody></table></td><td width="21%" valign="top" style="padding-left:5px;"><table class="grid other"><tr><th colspan="2" style="height:49px;">OTHER</th></tr><?php foreach($summary as $label=>$value){ ?><tr><td><?php echo $e($label); ?></td><td><b><?php echo $e($value); ?></b></td></tr><?php } ?></table></td></tr></table>
<div class="foot">* INTEGER is used as Internal Assessment in this demo. Each term: written 30+50+20=100, internal 10. Annual combined maximum: 220 per subject. Pass rule: at least <?php echo $e($config['pass_percent']); ?>% written marks in each term per subject, with no absence. Grade uses combined written + internal percentage.</div>
<div class="foot">Attendance uses recorded present/late/half-day entries, excluding holidays. Half-day counts as a full attended entry. Character: teacher-configured remark. <?php echo $e($config['note']); ?></div>
<table class="sign"><tr><td>________________________<br>Class Teacher's Sign.</td><td align="center">________________________<br>Guardian's Sign.</td><td align="right">________________________<br>Principal's Sign.</td></tr></table>
</div>
