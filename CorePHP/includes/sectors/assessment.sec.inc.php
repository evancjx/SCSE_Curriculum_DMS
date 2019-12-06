<table id="tblAssessment" class='table'>
  <tr><td class='label bbl'>Assessment</td></tr>
  <tr><td class='btl'>
    <table id='assessment' class='subtable'>
      <tr>
        <td class='label medCol'>Component</td>
        <td class='label testedLOCol'>Course LO Tested</td>
        <td class='label medCol'>Related Programme LO or Graduate Attributes</td>
        <td class='label weightageCol'>Weightage</td>
        <td class='label shortCol'>Team / Individual</td>
        <td class='label rubics'>Assessment Rubrics</td>
        <td class='action col'></td>
      </tr>
      <?php
        if($function == 'update' && !empty($data['assessment'])){
          $LOcount = count($data['learning_outcomes']);
          $gradAttrList = json_decode($gradAttr,true);

          foreach ($data['assessment'] as $index => $item){
            $display =
              "<tr id='assessment".$index."' class='assessmentRow'>".
              "<td><textarea type='text' name='component[]' placeholder='Component'>".$item['component']."</textarea></td>".
              "<td class='testedLOCol'>";
            if(!empty($data['assessmentLOTested'][$index])) $cosLOTested = preg_split("/[\s,]+/", $data['assessmentLOTested'][$index]);
            else unset($cosLOTested);
            for($lo = 1; $lo <= $LOcount; $lo++){
              if(!empty($cosLOTested) && in_array($lo, $cosLOTested)) $checked = "checked";
              else $checked = "";
              $display.= "<label><input type='checkbox' id='assessment".$index."LO".$lo."' name='assessment".$index."LO[]' value='".$lo."' ".$checked."/><span>".$lo."</span></label><br/>";
            }

            $display.="</td><td class='gradAttrCol'>".'<span class="gradAttrInfo">Hover mouse over text for more info.</span><br/><br/>';

            if(!empty($data['assessmentGradAttr'][$index])) $cosAssessmentGradAttr = preg_split("/[\s,]+/", $data['assessmentGradAttr'][$index]);
            else unset($cosAssessmentGradAttr);
            foreach($gradAttrList as $key => $gradAttr){
              if(!empty($cosAssessmentGradAttr) && in_array($gradAttr['ID'], $cosAssessmentGradAttr)) $checked = "checked";
              else $checked = "";
              $display.= '<label class="cbGradAttrLbl"><input type="checkbox" class="cbGradAttr" name="gradAttr'.$index.'[]" value="'.$gradAttr['ID'].'" '.$checked.'><span title="'.$gradAttr['main'].'&#13;&#10;'.$gradAttr['description'].'">'.$gradAttr['ID'].' '.$gradAttr['main'].'</span></label><br>';
            }
            $display.="</td>";

            if($item['category'] == "individual"){$i = "checked"; $t = "";}
            elseif ($item['category'] == "team") {$i = ""; $t = "checked";}
            $display.=
              "<td class='weightageCol'><input type='text' id='w".$index."' class='assessmentWeightageInput' name='weightage[]' placeholder='Percentage' value='".$item['weightage']."'/></td>".
              "<td>
                  <label><input type='radio' name='componentCat".$index."' value='individual' ".$i."/><span>Individual</span></label><br/>
                  <label><input type='radio' name='componentCat".$index."' value='team' ".$t."/><span>Team</span></label></td>".
              "<td><textarea type='text' name='assessmentRubrics[]' placeholder='Rubics info can be stored in appendix at the bottom'>".$item['rubrics']."</textarea></td>";

            if($index == 1) $display.="<td><input type='button' name='add' id='assessmentadd' value='Add'/></td>";
            else $display.='<td><input type="button" name="remove" id="'.$index.'" class="btn btn-danger assessment_remove" value="Del"></td>';

            echo $display;
          }
        }
        else {
          $display = "<tr id='assessment1' class='assessmentRow'>
              <td><textarea type='text' name='component[]' placeholder='Component'></textarea></td>
              <td id='testedLO' class='testedLOCol'></td>
              <td class='gradAttrCol'></td>
              <td class='weightageCol'><input type='text' id='w1' class='assessmentWeightageInput' name='weightage[]' placeholder='Percentage'/></td>
              <td>
                <label><input type='radio' name='componentCat1' value='individual' checked/><span>Individual</span></label><br/>
                <label><input type='radio' name='componentCat1' value='team' /><span>Team</span></label></td>
              <td><textarea type='text' name='assessmentRubrics[]' placeholder='Rubics info can be stored in appendix at the bottom'></textarea></td>
              <td><input type='button' name='add' id='assessmentadd' value='Add'/></td>
            </tr>";
          echo $display;
        }
      ?>
    </table>
  </tr>
</table>
<script>
$(document)
.on('change keyup paste click', '#tblAssessment #assessment', function(){
  if(document.getElementById('calWeightage') == null) {
    var calculateRow = "<tr id='calWeightage'><td colspan='3' class='calculation'>" +
      "Check percentage" +
      "</td><td id='totalWeightage'>" +
      "</td><td colspan='3'>" +
      "</td></tr>";
    $('#assessment').append(calculateRow);
  }
  var sumWeightage = 0;
  for (var i = 1; i <= $('#assessment .assessmentRow').length; i++){
    var value = parseInt(document.getElementById('w'+i).value, 10);
    if (isNaN(value)) value = 0;
    sumWeightage+= parseInt(value, 10);
  }
  if (isNaN(sumWeightage)) sumWeightage = 0
  document.getElementById('totalWeightage').innerHTML = sumWeightage + "%";
})
.ready(function(){
  function assessmentInit(){
    for (var c = $('#assessment tr').length; c <= 2; c++){
      var assessmentRow = '<tr id="assessment'+c+'" class="assessmentRow">' +
        '<td><textarea type="text"  name="component[]" placeholder="Component"></textarea></td>' +
        '<td id="testedLO" class="testedLOCol"></td>' +
        '<td class="gradAttrCol"></td>' +
        '<td class="weightageCol"><input type="text" id="w'+c+'" class="assessmentWeightageInput" name="weightage[]" placeholder="Percentage"/></td>' +
        '<td>' +
          '<label><input type="radio" name="componentCat'+c+'" value="individual" checked/><span>Individual</span></label><br/>' +
          '<label><input type="radio" name="componentCat'+c+'" value="team" /><span>Team</span></label></td>' +
        '<td><textarea type="text" name="assessmentRubrics[]" placeholder="Rubics info can be stored in appendix at the bottom"></textarea></td>' +
        '<td><input type="button" name="remove" id="'+c+'" class="btn btn-danger assessment_remove" value="Del"></td>' +
        '</tr>';
      $('#assessment'+(c-1).toString()).after(assessmentRow);
    }

    //Display checkbox for Learning Outcomes
    var assessmentRow = $('#tblAssessment #assessment tr').length - 1;
    var numLO = $('#LO tr').length;
    for (var i = 1; i <= assessmentRow; i++){
      for (var c = 1; c <= numLO; c++){
        $('#assessment'+i+' .testedLOCol').append('<label><input type="checkbox" id="assessment'+i+'LO'+c+'" name=assessment'+i+'LO[] value="'+c+'"/><span>'+c+'</span></label><br>');
      }
    }

    //Display checkbox for Graduate Attribute list
    jQuery.ajax({
      type:"POST",
      url:"includes/display.inc.php",
      datatype:"json",
      data:{type: 'gradAttr'},

      success: function (obj){
        var assessmentRow = $('#assessment .assessmentRow').length;
        for (var i = 1; i <= assessmentRow; i++){
          var gradAttrCheckbox = '<span class="gradAttrInfo">Hover mouse over text for more info.</span><br/><br/>';
          obj.forEach(function(item, index){
            gradAttrCheckbox += '<label class="cbGradAttrLbl"><input type="checkbox" class="cbGradAttr" name="gradAttr'+i+'[]" value="'+item['ID']+'"><span title="'+item['main']+'&#13;&#10;'+item['description']+'">'+item['ID']+' '+item['main']+'</span></label><br>';
          });
          $('#assessment'+i+' .gradAttrCol').append(gradAttrCheckbox);
        }
      }
    })
  }
  //Display Learning outcomes (check if it is update)
  var bUpdate = false;
  var query = window.location.search.substring(1).split("&");
  for(var i = 0; i < query.length; i++){
    var pair = query[i].split("=");
    if(pair[0] == 'function' && pair[1] == 'update') bUpdate = true;
  }
  if(bUpdate){
    if(document.getElementById('assessment1LO1') == undefined){
      assessmentInit();
    }
  }
  else{
    assessmentInit();
  }

  $('#assessmentadd').click(function(){
    var numAssessment = $('#assessment .assessmentRow').length + 1;
    var assessmentRow = '<tr id="assessment'+numAssessment+'" class="assessmentRow">' +
      '<td><textarea type="text"  name="component[]" placeholder="Component"></textarea></td>' +
      '<td id="testedLO" class="testedLOCol"></td>' +
      '<td id="GradAttr" class="gradAttrCol"></td>' +
      '<td class="weightageCol"><input type="text" id="w'+numAssessment+'" class="assessmentWeightageInput" name="weightage[]" placeholder="Percentage"/></td>' +
      '<td>' +
        '<label><input type="radio" name="componentCat'+numAssessment+'" value="individual" checked/><span>Individual</span></label><br/>' +
        '<label><input type="radio" name="componentCat'+numAssessment+'" value="team" /><span>Team</span></label></td>' +
        '<td><textarea type="text" name="assessmentRubrics[]" placeholder="Rubics info can be stored in appendix at the bottom"></textarea></td>' +
        '<td><input type="button" name="remove" id="'+numAssessment+'" class="btn btn-danger assessment_remove" value="Del"></td>' +
      '</tr>';
    $('#assessment'+(numAssessment-1)).after(assessmentRow);

    var numLO = $('#LO tr').length;
    for (var c = 1; c <= numLO; c++){
      $('#assessment'+numAssessment+' .testedLOCol').append('<label><input type="checkbox" name=assessment'+numAssessment+'LO[]/><span>'+c+'</span></label><br>');
    }

    jQuery.ajax({
      type:"POST",
      url:"includes/display.inc.php",
      datatype:"json",
      data:{type: 'gradAttr'},

      success: function (obj){
        var gradAttrCheckbox = '<span class="gradAttrInfo">Hover mouse over text for more info.</span><br><br>';
        obj.forEach(function(item, index){
          gradAttrCheckbox += '<label class="cbGradAttrLbl"><input type="checkbox" class="cbGradAttr" name="gradAttr'+numAssessment+'[]" value="'+item['ID']+'"><span title="'+item['main']+'&#13;&#10;'+item['description']+'">'+item['ID']+' '+item['main']+'</span></label><br>';
        });
        $('#assessment'+numAssessment+' .gradAttrCol').append(gradAttrCheckbox);
      }
    });
  });

  $(document).on('click', '.assessment_remove', function(){
    var button_id = $(this).attr("id");
    $('#assessment'+button_id+'').remove();
  });

  function updateLOcheckbox(){
    var numLO = $('#LO tr').length;
    var assessmentRow = $('#assessment tr').length;
    for (var i = 1; i < assessmentRow; i++){
      $('#assessment'+i+' .testedLOCol').remove();
      $('#assessment'+i+' .gradAttrCol').before('<td id="testedLO" class="testedLOCol"></td>');
      for (var c = 1; c <= numLO; c++){
        $('#assessment'+i+' .testedLOCol').append('<label><input type="checkbox" id="assessment'+i+'LO'+c+'" name=assessment'+i+'LO[] value="'+c+'"/><span>'+c+'</span></label><br>');
      }
    }
    if(bUpdate){
      jQuery.ajax({
        type:"POST",
        url:"includes/display.inc.php",
        datatype:"json",
        data:{type: 'cosLOTested', courseCode:'<?php echo $data['courseMainDetails']['code']?>'},
      })
      .done(function ajaxDone(data){
        data.forEach(function(item, index){
          document.getElementById("assessment"+item['assessmentID']+"LO"+item['learningOutcomesID']).checked = true;
        })
      })
      .fail(function ajaxFailed(e){
        console.log(e.responseText);
      })
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
