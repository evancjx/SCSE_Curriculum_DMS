<table id="tblContent" class='table'>
  <tr><td class='label bbl'>Course Contents</td></tr>
  <tr><td class='btl'>
    <table id='cosContents' class='subtable'>
      <tr>
        <td class='short'>S/N</td>
        <td class='label'>Topics</td>
        <td class='att1'><textarea type='text' name='cosContentAtt1' placeholder="Attribute 1"><?php if($function == 'update') echo($data['cosContentAtt']['att1']);?></textarea></td>
        <td class='att2'><textarea type='text' name='cosContentAtt2' placeholder="Attribute 2"><?php if($function == 'update') echo($data['cosContentAtt']['att2']);?></textarea></td>
        <td class='TOPICaction col'></td>
      </tr>
      <?php
      if($function == 'update' && !empty($data['content'])){
        $array_keys = array_keys($data['content']);
        $firstKey = $array_keys[0];
        foreach ($data['content'] as $key => $content){
          $display =
            "<tr id='topics".$key."'>".
            "<td class='short sn'><input type='text' id='SN' name='ID[]' placeholder='S/N' value='".$key."'/></td>".
            "<td class='des'><textarea type='text' name='topics[]' placeholder='Topics description'>".$content['topic']."</textarea></td>".
            "<td class='att1'><textarea type='text' name='cosContentDetails1[]' placeholder='Details'>".$content['details1']."</textarea></td>";

          if($content['rowspan'] != 0){
            $display.=
            "<td class='att2' rowspan = ".$content['rowspan'].">".
            "<textarea type='text' name='cosContentDetails2[]' placeholder='Details'>".$content['details2']."</textarea>".
            "<label>";
            if($content['rowspan'] > 1){
              $display.= "<input type='checkbox' id='".$key."' class='attDetailMerge' name='merge[]' value='".$key."' checked/>Merge bottom</label></td>";
            }
            else{
              $display.= "<input type='checkbox' id='".$key."' class='attDetailMerge' name='merge[]' value='".$key."'/>Merge bottom</label></td>";
            }
          }
          else {
            $display.= "<td class='att2' style='display:none'>".
            "<textarea type='text' name='cosContentDetails2[]' placeholder='Details'>".$content['details2']."</textarea>".
            "<label>Merge bottom</label><input type='checkbox' id='".$key."' class='attDetailMerge' name='merge[]' value='".$key."'/></td>";
          }
          echo $display;
          if ($key > $firstKey){
            echo "<td class='action col'><input type='button' name='remove' id='".$key."' class='btn btn-danger content_remove' value='Del'></td></tr>";
          }
          else {
            echo "<td class='action col'><input type='button' name='add' id='topicadd' value='Add'/></td></tr>";
          }
        }
      }
      else{
        echo "<tr id='topics1'>
          <td class='short sn'><input type='text' id='SN' name='ID[]' placeholder='S/N' value='1'/></td>
          <td class='des'><textarea type='text' name='topics[]' placeholder='Topics description'></textarea></td>
          <td class='att1'><textarea type='text' name='cosContentDetails1[]' placeholder='Details'></textarea></td>
          <td class='att2'><textarea type='text' name='cosContentDetails2[]' placeholder='Details'></textarea>
          <label><input type='checkbox' id='1' class='attDetailMerge' name='merge[]' value='1'/>Merge bottom</label></td>
          <td class='action col'><input type='button' name='add' id='topicadd' value='Add'/></td>
        </tr>";
      }
      ?>
    </table>
  </td></tr>
</table>
<script>
$(document).ready(function(){
  for (var c = $('#cosContents tr').length; c <= 4; c++){
    $('#cosContents').append('<tr id="topics'+c+'">'+
        '<td class="short sn"><input type="text" id="SN" name="ID[]" placeholder="S/N" value="'+c+'"/></td>'+
        '<td class="des"><textarea type="text" name="topics[]"" placeholder="Topics description"></textarea></td>'+
        '<td class="att1"><textarea type="text" name="cosContentDetails1[]" placeholder="Details"/></textarea></td>'+
        '<td class="att2"><textarea type="text" name="cosContentDetails2[]" placeholder="Details"/></textarea>'+
        '<label><input type="checkbox" id="'+c+'" class="attDetailMerge" name="merge[]" value="'+c+'"/>Merge bottom</label></td>'+
        '<td class="action col"><input type="button" name="remove" id="'+c+'" class="btn btn-danger content_remove" value="Del"></td></tr>');
  }
  $('#topicadd').click(function(){
    var top = parseInt($('#SN').prop('value'), 10) + parseInt($('#cosContents tr #SN').length, 10);
    $('#cosContents').append('<tr id="topics'+top+'">'+
        '<td class="short sn"><input type="text" id="SN" name="ID[]" placeholder="S/N" value="'+top+'"/></td>'+
        '<td class="des"><textarea type="text" name="topics[]"" placeholder="Topics description"></textarea></td>'+
        '<td class="att1"><textarea type="text" name="cosContentDetails1[]" placeholder="Details"/></textarea></td>'+
        '<td class="att2"><textarea type="text" name="cosContentDetails2[]" placeholder="Details"/></textarea>'+
        '<label><input type="checkbox" id="'+top+'" class="attDetailMerge" name="merge[]" value="'+top+'"/>Merge bottom</label></td>'+
        '<td class="action col"><input type="button" name="remove" id="'+top+'" class="btn btn-danger content_remove" value="Del"></td></tr>');
  });
  $(document).on('click', '.content_remove', function(){
    var delete_row = parseInt($(this).attr("id"), 10);
    // console.log("Delete row: " + delete_row);
    $('#topics'+delete_row+'').remove();
    var previous_row =  parseInt(delete_row, 10) - 1;
    // console.log("Previous row: " + previous_row);
    if ($('#topics'+previous_row+' .att2 .attDetailMerge').prop('checked')==true){
      $('#topics'+previous_row+' .att2').removeAttr('rowspan');
      $('#topics'+previous_row+' .att2 .attDetailMerge').prop('checked', false);
    }
    else {
      var next_row = 1 + parseInt(delete_row, 10);
      // console.log("Next row: " + next_row);
      $('#topics'+next_row+' .att2').removeAttr('style');
    }
  });
  $(document).on('click', '.attDetailMerge', function(){
    var topic_row = parseInt($(this).attr("id"),10);
    var delete_row = topic_row + 1;
    if ($(this).prop('checked')==true){
      $('#topics'+topic_row+' .att2').attr('rowspan', 2);
      $('#topics'+delete_row+' .att2').attr('style', 'display:none');
    }
    else{
      $('#topics'+topic_row+' .att2').removeAttr('rowspan');
      $('#topics'+delete_row+' .att2').removeAttr('style');
    }
  });
});
</script>
