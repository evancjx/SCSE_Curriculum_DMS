<table id="tblPlannedWeeklySchedule" class='table'>
  <tr><td class='label bbl'>Planned Weekly Schedule</td></tr>
  <tr><td class='btl'>
    <table id="PlannedWeeklySchedule" class="subtable">
      <tr>
        <td class='label shortCol'>Week</td>
        <td class='label medCol'>Topic</td>
        <td class='label shortCol'>Course LO</td>
        <td class='label shortCol'>Readings</td>
        <td class='label medCol'>Example Activities</td>
        <td class='courseInstructorsAction'></td>
      </tr>
      <?php
      if($function == 'update' && !empty($data['schedule'])){
        $index = 0;
        foreach($data['schedule'] as $key => $item){
          $index = $index + 1;
          $display = "<tr id='plannedSchedule".$key."'><td>".
            "<input type='text' name='scheduleWeek[]' placeholder='Week' value='".$key."'/>".
            "</td><td>".
            "<textarea type='text' name='scheduleTopic[]' placeholder='Topic'>".$item['topic']."</textarea>".
            "</td><td id='scheduleLO".$key."'>".
            "</td><td class='scheduleReadings'>".
            "<textarea type='text' name='scheduleReadings[]' placeholder='Readings'>".$item['readings']."</textarea>".
            "</td><td>".
            "<textarea type='text' name='scheduleActivities[]' placeholder='Activities'>".$item['activities']."</textarea>".
            "</td><td class='action'>";
          if($index == 1)
            $display.= "<input type='button' name='add' id='scheduleAdd' value='Add'/>";
          else {
            $display.= '<input type="button" name="remove" id="'.$key.'" class="btn btn-danger scheduleRemove" value="Del">';
          }
          $display.="</td></tr>";
          echo $display;
        }
      }
      else{
        echo "<tr id='plannedSchedule1'><td>".
          "<input type='text' name='scheduleWeek[]' placeholder='Week' value='1'/>".
          "</td><td>".
          "<textarea type='text' name='scheduleTopic[]' placeholder='Topic'></textarea>".
          "</td><td id='scheduleLO1'>".
          "</td><td class='scheduleReadings'>".
          "<textarea type='text' name='scheduleReadings[]' placeholder='Readings'></textarea>".
          "</td><td>".
          "<textarea type='text' name='scheduleActivities[]' placeholder='Activities'></textarea>".
          "</td><td class='action'>".
          "<input type='button' name='add' id='scheduleAdd' value='Add'/>".
          "</td></tr>";
      }
    ?>
    </table>
  </td></tr>
</table>
<script>
$(document).ready(function(){
  function scheduleInit(){
    for(var i = $('#PlannedWeeklySchedule tr').length; i<= 8; i++){
      var scheduleRow = '<tr id="plannedSchedule'+i+'"><td>' +
        '<input type="text" name="scheduleWeek[]" placeholder="Week" value="'+i+'"/>' +
        '</td><td>' +
        "<textarea type='text' name='scheduleTopic[]' placeholder='Topic'></textarea>" +
        "</td><td id='scheduleLO"+i+"'>" +
        '</td><td class="scheduleReadings">' +
        "<textarea type='text' name='scheduleReadings[]' placeholder='Readings'></textarea>" +
        '</td><td>' +
        "<textarea type='text' name='scheduleActivities[]' placeholder='Activities'></textarea>" +
        '</td><td>' +
        '<input type="button" name="remove" id="'+i+'" class="btn btn-danger scheduleRemove" value="Del">' +
        '</td></tr>';
      $('#PlannedWeeklySchedule').append(scheduleRow);
    }
  }
  //Display Learning outcomes (check if it is update)
  var bUpdate = false;
  var query = window.location.search.substring(1).split("&");
  for(var i = 0; i < query.length; i++){
    var pair = query[i].split("=");
    if(pair[0] == 'function' && pair[1] == 'update') bUpdate = true;
  }
  if(bUpdate){
    jQuery.ajax({
      type:"POST",
      url:"includes/display.inc.php",
      datatype:"json",
      data:{type: 'scheduleLO',courseCode: '<?php if($function == 'update') echo $data['courseMainDetails']['code'];?>'},

      success: function (obj){
        obj.forEach(function(item, index){
          document.getElementById(item['scheduleWeek']+'LO'+item['loID']).checked = true;
        });
      }
    });
    if($('#PlannedWeeklySchedule tr').length < 8) scheduleInit();
  }
  else{
    scheduleInit()
  }


  var numSchedule = $('#PlannedWeeklySchedule tr').length - 1;
  var numLO = $('#LO tr').length;
  for (var i = 1; i <= numSchedule; i++){
    for (var c = 1; c <= numLO; c++){
      $('#plannedSchedule'+i+' #scheduleLO'+i+'').append('<label><input type="checkbox" name=scheduleLO'+i+'[] id="'+i+'LO'+c+'" value="'+c+'"/><span>'+c+'</span></label><br>');
    }
  }
  $('#scheduleAdd').click(function(){
    var numSchedule =  $('#PlannedWeeklySchedule tr').length;
    var scheduleRow = '<tr id="plannedSchedule'+numSchedule+'"><td>' +
      '<input type="text" name="scheduleWeek[]" placeholder="Week" value="'+numSchedule+'"/>' +
      '</td><td>' +
      "<textarea type='text' name='scheduleTopic[]' placeholder='Topic'></textarea>" +
      "</td><td id='scheduleLO"+numSchedule+"'>" +
      '</td><td class="scheduleReadings">' +
      "<textarea type='text' name='scheduleReadings[]' placeholder='Readings'></textarea>" +
      '</td><td>' +
      "<textarea type='text' name='scheduleActivities[]' placeholder='Activities'></textarea>" +
      '</td><td>' +
      '<input type="button" name="remove" id="'+numSchedule+'" class="btn btn-danger scheduleRemove" value="Del">' +
      '</td></tr>';
    $('#PlannedWeeklySchedule').append(scheduleRow);

    for (var c = 1; c <= numLO; c++){
      $('#plannedSchedule'+numSchedule+' #scheduleLO'+numSchedule+'').append('<label><input type="checkbox" name=scheduleLO'+numSchedule+'[] value="'+c+'"/><span>'+c+'</span></label><br>');
    }
  });

  $(document).on('click', '.scheduleRemove', function(){
    var button_id = $(this).attr("id");
    $('#plannedSchedule'+button_id+'').remove();
  });

  function updateLOcheckbox(){
    var numLO = $('#LO tr').length;
    var numSchedule = $('#PlannedWeeklySchedule tr').length;
    for (var i = 1; i < numSchedule; i++){
      $('#plannedSchedule'+i+' #scheduleLO'+i+'').remove();
      $('#plannedSchedule'+i+' .scheduleReadings').before("<td id='scheduleLO"+i+"'></td>");
      for (var c = 1; c <= numLO; c++){
        $('#plannedSchedule'+i+' #scheduleLO'+i+'').append('<label><input type="checkbox" name=scheduleLO'+i+'LO[] id="'+i+'LO'+c+'" value="'+c+'"/><span>'+c+'</span></label><br>');
      }
    }
  }

  //Update Course LO tested checkbox if any new Course Learning Outcome is added.
  $('#loadd').click(function(){
    updateLOcheckbox();
  });

  $(document).on('click', '.LO_remove', function(){
    updateLOcheckbox();
  });
});
</script>
