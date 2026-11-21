<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content-wrapper"><section class="content-header"><h1>Photo Style Annual Progress Card</h1></section><section class="content"><div class="box box-primary"><div class="box-body">
<p>This optional format combines Half Yearly and Annual assessments. Existing marksheet formats remain available.</p>
<?php if($error){ ?><div class="alert alert-danger"><?php echo html_escape($error); ?></div><?php } ?>
<?php if($this->session->flashdata('msg')){ ?><div class="alert alert-success">Settings saved.</div><?php } ?>
<form method="post" action="<?php echo site_url('admin/progresscard?template_id='.$template_id); ?>"><?php echo $this->customlib->getCSRF(); ?>
<div class="form-group"><label>School Name</label><input class="form-control" name="school_name" maxlength="150" required value="<?php echo html_escape($config['school_name']); ?>"></div>
<div class="row"><?php foreach(array('h1'=>'Half Yearly - First Paper /30','h2'=>'Half Yearly - Second Paper /50','hm'=>'Half Yearly - Monthly Test /20','hi'=>'Half Yearly - Integer (Internal) /10','a1'=>'Annual - First Paper /30','a2'=>'Annual - Second Paper /50','am'=>'Annual - Monthly Test /20','ai'=>'Annual - Integer (Internal) /10') as $key=>$label){ ?>
<div class="col-sm-6 form-group"><label><?php echo html_escape($label); ?></label><select class="form-control" name="<?php echo $key; ?>"><?php foreach($exams as $exam){ ?><option value="<?php echo (int)$exam->id; ?>" <?php echo (int)$config['exams'][$key]===(int)$exam->id?'selected':''; ?>><?php echo html_escape($exam->exam); ?></option><?php } ?></select></div><?php } ?></div>
<div class="form-group"><label>Character remark</label><input class="form-control" name="character" value="<?php echo html_escape($config['character']); ?>"></div>
<div class="form-group"><label>Report note</label><input class="form-control" name="note" value="<?php echo html_escape($config['note']); ?>"></div>
<p>Enter or edit marks under Examinations → Exam Group in the mapped exams. Missing marks show Pending; they are not treated as a pass. Integer means internal assessment for this configured format. Rank is within the student's class and section.</p>
<button type="submit" class="btn btn-primary">Save Progress Card Settings</button> <a class="btn btn-default" href="<?php echo site_url('admin/examresult/marksheet'); ?>">Print Marksheet</a> <a class="btn btn-default" href="<?php echo site_url('admin/marksheet'); ?>">Design Marksheet</a>
</form></div></div></section></div>
