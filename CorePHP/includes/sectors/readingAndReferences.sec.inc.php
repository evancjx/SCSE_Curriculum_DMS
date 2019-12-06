<table id="tblReadingAndReferences" class='table'>
  <tr><td class='label'>Reading and References</td></tr>
  <tr><td class='bbl'>The course will not use any specific text book. The following books and websites will be used as reference materials.</td></td>
  <tr><td class='btl'>
    <table id="ReadingAndReferences" class="subtable">
      <tr>
        <td class='label'>Details</td>
        <td class='action'></td>
      </tr>
      <?php
      if($function == 'update' && !empty($data['reference'])){
        foreach($data['reference'] as $key => $content){
          $display = "<tr id='approach".$key."'><td>".
            "<textarea type='text' name='references[]' placeholder='References Details'>".$content."</textarea>".
            "</td><td class='action'>";
          if($key == 1)
            $display.= "<input type='button' name='add' id='referencesAdd' value='Add'/>";
          else
            $display.= "<input type='button' name='remove id='".$key."' class='btn btn-danger referencesRemove' value='Del'/>";
          $display.="</td></tr>";
          echo $display;
        }
      }
      else{
        echo "<tr id='references1'><td>".
          "<textarea type='text' name='references[]' placeholder='References Details'></textarea>".
          "</td><td class='action'>".
          "<input type='button' name='add' id='referencesAdd' value='Add'/>".
          "</td></tr>";
      }
    ?>
    </table>
  </td></tr>
</table>
<script>
$(document).ready(function(){
  $('#referencesAdd').click(function(){
    var numReferences = $('#ReadingAndReferences tr').length - 1;
    var row = '<tr id="references'+(numReferences+1)+'"><td>' +
      '<textarea type="text" name="references[]" placeholder="References Details"></textarea>' +
      '</td><td class="action">' +
      '<input type="button" name="remove" id="'+(numReferences+1)+'" class="btn btn-danger referencesRemove" value="Del">' +
      '</td></tr>';
    $('#ReadingAndReferences').append(row);
  });
  $(document).on('click', '.referencesRemove', function(){
    var button_id = $(this).attr("id");
    $('#references'+button_id+'').remove();
  });
});
</script>
