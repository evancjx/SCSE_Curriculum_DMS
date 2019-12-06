<table id="tblLearningAndTeachingApproach" class='table'>
  <tr><td class='label bbl'>Learning and Teaching approach</td></tr>
  <tr><td class='btl'>
    <table id="LearningAndTeachingApproach" class="subtable">
      <tr>
        <td class='label medCol'>Approach</td>
        <td class='label'>How does this approach support students in achieving the learning outcomes?</td>
        <td class='action'></td>
      </tr>
      <?php
        if($function == 'update' && !empty($data['approach'])){
          foreach($data['approach'] as $key => $content){
            $display = "<tr id='approach".$key."'><td>".
              "<textarea type='text' name='approachMain[]' placeholder='Approach Name'>".$content['main']."</textarea>".
              "</td><td>".
              "<textarea type='text' name='approachDescription[]' placeholder='Approach Description'>".$content['description']."</textarea>".
              "</td><td class='action'>";
            if($key == 1)
              $display.= "<input type='button' name='add' id='approachAdd' value='Add'/>";
            else
              $display.= "<input type='button' name='remove id='".$key."' class='btn btn-danger approachRemove' value='Del'/>";
            $display.="</td></tr>";
            echo $display;
          }
        }
        else{
          echo "<tr id='approach1'><td>".
            "<textarea type='text' name='approachMain[]' placeholder='Approach Name'></textarea>".
            "</td><td>".
            "<textarea type='text' name='approachDescription[]' placeholder='Approach Description'></textarea>".
            "</td><td class='action'>".
            "<input type='button' name='add' id='approachAdd' value='Add'/>".
            "</td></tr>";
        }
      ?>
    </table>
  </td></tr>
</table>
<script>
$(document).ready(function(){
  $('#approachAdd').click(function(){
    var numApproach = $('#LearningAndTeachingApproach tr').length - 1;
    var row = '<tr id="approach'+(numApproach+1)+'"><td>' +
      '<textarea type="text" name="approachMain[]" placeholder="Approach Name"></textarea>' +
      '</td><td>' +
      '<textarea type="text" name="approachDescription[]" placeholder="Approach Description"></textarea>' +
      '</td><td class="action">' +
      '<input type="button" name="remove" id="'+(numApproach+1)+'" class="btn btn-danger approachRemove" value="Del">' +
      '</td></tr>';
    $('#LearningAndTeachingApproach').append(row);
  });
  $(document).on('click', '.approachRemove', function(){
    var button_id = $(this).attr("id");
    $('#approach'+button_id+'').remove();
  });
});
</script>
