<?php $form = new Form(); ?>
<table id='courseDetails' class="table">
  <tr>
    <td class='label'>Academic Year</td>
    <td colspan="2"></td>
    <td class='label' colspan="2">Semester</td>
    <td colspan="2"></td></tr>
  <tr>
    <td class='label'>Author(s)</td>
    <td class='details' colspan="6"></td></tr>
  <tr>
    <td class='label'>Course Code</td>
    <td class='' colspan="4">
      <input type="text" id="courseCode" name="code" value="<?php if($function == 'update') echo $data['courseMainDetails']['code'];?>" placeholder="Course Code">
    </td>
    <td colspan="2">
      <label class="">
        <input id="course_CE" class=" course_rep" type="checkbox" name="course[rep][]" value="CE" <?php if($function == 'update' && in_array('CE', explode('/', $data['courseMainDetails']['course']))) echo 'checked'; else if($function == 'update'); else echo 'checked'; ?>/>CE
      </label>
      <label class="">
        <input id="course_CZ" class=" course_rep" type="checkbox" name="course[rep][]" value="CZ" <?php if($function == 'update' && in_array('CZ', explode('/', $data['courseMainDetails']['course']))) echo 'checked'; else if($function == 'update'); else echo 'checked'; ?>/>CZ
      </label>
    </td>
  </tr>
  <tr>
    <td class='label'>Course Title</td>
    <td class='details' colspan="6">
      <input type="text" id="courseTitle" name="title" value="<?php if($function == 'update')echo $data['courseMainDetails']['title'] ?>" placeholder="Course Title"></td></tr>
  <tr>
    <td class='label'>Pre-requisites</td>
    <td class='details' colspan="6">
      <input type="text" id="prerequisite" name="prerequisite" class="prerequisite" value="<?php
        if($function == 'update' && !empty($data['prerequisite'])){
          foreach($data['prerequisite'] as $key => $item){
            if($key > 0) echo ", ";
            echo $item;
          }
        }
        else{ echo 'NIL'; }?>" placeholder="Pre-requisite Course codes [Leave blank if nil]"></td></tr>
  <tr>
    <td class='label'>No of AUs</td>
    <td class='details' colspan="6">
      <input type="text" name="noAU" value="<?php if($function == 'update')echo $data['courseMainDetails']['noAU'] ?>" placeholder="Number of Academic Units"></td></tr>
  <tr>
    <td class='label'>Contact Hours</td>
    <td class='label'>
      <?php $form->displaySelectCHoption(1, strtolower(preg_replace('/\s/', '', $data['displayCH'][0]))); ?></td>
    <td class='input'>
      <input type="text" name="chInput1" value="<?php if($function == 'update')echo $data['courseMainDetails'][strtolower(preg_replace('/\s/', '', $data['displayCH'][0]))] ?>" placeholder="hours"></td>
    <td class='label'>
      <?php $form->displaySelectCHoption(2, strtolower(preg_replace('/\s/', '', $data['displayCH'][1]))); ?></td>
    <td class='input'>
      <input type="text" name="chInput2" value="<?php if($function == 'update')echo $data['courseMainDetails'][strtolower(preg_replace('/\s/', '', $data['displayCH'][1]))] ?>" placeholder="hours"></td>
    <td class='label'>
      <?php $form->displaySelectCHoption(3, strtolower(preg_replace('/\s/', '', $data['displayCH'][2]))); ?></td>
    <td class='input'>
      <input type="text" name="chInput3" value="<?php if($function == 'update')echo $data['courseMainDetails'][strtolower(preg_replace('/\s/', '', $data['displayCH'][2]))] ?>" placeholder="hours"></td></tr>
  <tr>
    <td class='label'>Proposal Date</td><td class='details' colspan="6"></td></tr>
</table>
<link rel="stylesheet" href="/javascript/token-input.css" />
<script src="/javascript/jquery.tokeninput.js"></script>
<script>
$(document).ready(function(){
  $('#prerequisite').autocomplete({
    source: 'includes/display.inc.php'
  });
  // $("#prerequisite").tokenInput("includes/display.inc.php",{
  //   hintText: "Type course code...",
  //   noResultsText: "Code not found.",
  //   searchingText: "Searching..."
  // });
	var listDisabled = [];
	$('select').change(function(){
		if(this.selectedIndex != 0)
			listDisabled[$(this).attr('id')] = this.selectedIndex;
		else
			delete listDisabled[$(this).attr('id')];

    $('select option').each(function(){
      $(this).attr("disabled", false);
    })
		$('select option').each(function(){
      for(var i = 1; i <= listDisabled.length-1; i++){
        if(parseInt($(this.parentElement).attr('id'),10) == i) continue;
        else if(listDisabled[i] == this.index)
          $(this).attr("disabled", true);
      }
		});
	});
});
</script>
