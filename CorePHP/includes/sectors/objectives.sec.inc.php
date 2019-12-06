<table id='tblObjectives' class="table">
  <tr><td class='label'>Course Aims</td></tr>
  <tr><td class='aim'>
    <textarea type="text" id="courseAims" name="objectives[courseAims]" placeholder="Objectives: Course Aims"><?php if($function == 'update')echo $data['courseAims'] ?></textarea></td></tr>
  <tr><td class='label'>Intended Learning outcomes</td></tr>
  <tr><td class='ilo'>By the end of this course, the student would be able to:<br>
    <table id='LO' class='subtable' >
        <?php
        if($function == 'update' && !empty($data['learning_outcomes'])){
          foreach ($data['learning_outcomes'] as $key => $value) {
            // echo $value;
            echo
              "<tr id='lo".$key."'>".
              "<td class='iLO'><textarea type='text' name='objectives[LO][]' id='iLO".$key."' class='form-control name_list' placeholder='Learning Outcomes'/>".$value."</textarea></td>";
            if ($key > 1){
              echo '<td class="action col"><input type="button" name="remove" id="'.$key.'" class="btn btn-danger LO_remove" value="Del"></td></tr>';
            }
            else{
              echo "<td class='action col'><input type='button' name='add' id='loadd' value='Add'/></td></tr>";
            }
          }
        }
        else {
          echo "<tr id='lo1'><td class='iLO'><textarea type='text' name='objectives[LO][]' id='iLO1' class='form-control name_list' placeholder='Learning Outcomes'/></textarea></td>
          <td class='action col'><input type='button' name='add' id='loadd' value='Add'/></td></tr>";
        }?>
    </table>
  </td></tr>
</table>
<script>
$(document).ready(function(){
  var numLO = $('#LO tr').length;
  for (var c = numLO; c < 4; c++){
    $('#LO').append('<tr id="lo'+(c+1)+'">'+
        '<td class="iLO"><textarea type="text" name="objectives[LO][]" id="iLO'+(c+1)+'" class="form-control name_list" placeholder="Learning Outcomes" /></textarea></td>'+
        '<td class="action col"><input type="button" name="remove" id="'+(c+1)+'" class="btn btn-danger LO_remove" value="Del"></td></tr>');
  }
  $('#loadd').click(function(){
    var numLO = $('#LO tr').length + 1;
    $('#LO').append('<tr id="lo'+numLO+'">'+
        '<td class="iLO"><textarea type="text" name="objectives[LO][]" id="iLO'+numLO+'" class="form-control name_list" placeholder="Learning Outcomes" /></textarea></td>'+
        '<td class="action col"><input type="button" name="remove" id="'+numLO+'" class="btn btn-danger LO_remove" value="Del"></td></tr>');

  });
  $(document).on('click', '.LO_remove', function(){
    var button_id = $(this).attr("id");
    $('#lo'+button_id+'').remove();
    numLO--;
  });
});
</script>
