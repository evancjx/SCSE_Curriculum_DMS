<table id="tblCourseInstructor" class='table'>
  <tr><td class='label bbl'>Course Instructor</td></tr>
  <tr><td class='btl'>
    <table id="CourseInstructor" class="subtable">
      <tr>
        <td class='label medCol'>Instructor</td>
        <td class='label medCol'>Office Location</td>
        <td class='label medCol'>Phone</td>
        <td class='label medCol'>Email</td>
        <td class='action'></td>
      </tr>
      <?php
      if($function == 'update' && !empty($data['courseInstructors'])){
        $count = 0;
        foreach($data['courseInstructors'] as $key => $content){
        $count++;
          $display = "<tr id='instructor".$count."'><td>".
            "<input type='text' name='instructorName[]' placeholder='Name' value='".$content['instructorName']."'/>".
            "</td><td>".
            "<input type='text' name='instructorOffice[]' placeholder='Office Location' value='".$content['instructorOffice']."'/>".
            "</td><td>".
            "<input type='text' name='instructorPhone[]' placeholder='Phone' value='".$content['instructorPhone']."'/>".
            "</td><td>".
            "<input type='text' name='instructorEmail[]' placeholder='Email' value='".$content['instructorEmail']."'/>".
            "</td><td class='action'>";
          if($count == 1)
            $display.= "<input type='button' name='add' id='instrutorAdd' value='Add'/>";
          else
            $display.= "<input type='button' name='remove' id='".$count."' class='btn btn-danger instructorRemove' value='Del'>";
          $display.= "</td></tr>";
          echo $display;
        }
      }
      else{
        echo "<tr id='instructor1'><td>".
          "<input type='text' name='instructorName[]' placeholder='Name'/>".
          "</td><td>".
          "<input type='text' name='instructorOffice[]' placeholder='Office Location'/>".
          "</td><td>".
          "<input type='text' name='instructorPhone[]' placeholder='Phone'/>".
          "</td><td>".
          "<input type='text' name='instructorEmail[]' placeholder='Email'/>".
          "</td><td class='action'>".
          "<input type='button' name='add' id='instrutorAdd' value='Add'/>".
          "</td></tr>";
      }
    ?>
    </table>
  </td></tr>
</table>
<script>
$(document).ready(function(){
  $('#instrutorAdd').click(function(){
    var numInstructor = $('#CourseInstructor tr').length - 1;
    var row = '<tr id="instructor'+(numInstructor+1)+'"><td>' +
      '<input type="text" name="instructorName[]" placeholder="Name"/>' +
      '</td><td>' +
      '<input type="text" name="instructorOffice[]" placeholder="Office Location"/>' +
      '</td><td>' +
      '<input type="text" name="instructorPhone[]" placeholder="Phone"/>' +
      '</td><td>' +
      '<input type="text" name="instructorEmail[]" placeholder="Email"/>' +
      '</td><td class="action">' +
      '<input type="button" name="remove" id="'+(numInstructor+1)+'" class="btn btn-danger instructorRemove" value="Del">' +
      '</td></tr>';
    $('#CourseInstructor').append(row);
  });
  $(document).on('click', '#CourseInstructor .instructorRemove', function(){
    var button_id = $(this).attr("id");
    console.log('here');
    $('#instructor'+button_id+'').remove();
  });
});
</script>
