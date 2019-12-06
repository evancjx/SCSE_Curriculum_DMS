<table id="tblMapGraduateAttributes" class='table'>
  <tr><td class='label bbl'>Mapping of Course SLOs to EAB Graduate Attibutes</td></tr>
  <tr><td class='btl'>
    <table id="MapGraduateAttributes" class="subtable">
      <tr>
        <?php $gradAttrList = json_decode($c->getGraduateAttribute(),true);?>
        <td class='label longCol' rowspan="2">Course Student Learning Outcomes</td>
        <td class='label catCol' rowspan="2">Cat</td>
        <td class='label longCol' colspan="<?php echo sizeof($gradAttrList) ?>">EAB's <?php echo sizeof($gradAttrList) ?> Graudate Attributes*</td>
        <td class='label reqCol' colspan="2">EAB's CE/CS Requirement</td>
      </tr>
      <tr>
        <?php
        foreach($gradAttrList as $key => $gradAttr){
          $display = "<td class='shortCol'>";
          $display.= '<label><span title="'.$gradAttr['main'].'&#13;&#10;'.$gradAttr['description'].'">';
          $display.= "(".$gradAttr['ID'].")</span></label></td>";
          echo $display;
        }
        ?>
        <td>CE</td>
        <td>CS</td>
      </tr>
      <tr>
        <td id="courseLabel"><?php if($function == 'update') echo $data['courseMainDetails']['course'].$data['courseMainDetails']['code']." ".$data['courseMainDetails']['title']?></td>
        <td>Core</td>
        <?php
        foreach($gradAttrList as $key => $gradAttr){
          $display = "<td class='shortCol'><input type='text' id='gradATTR".$gradAttr['ID']."' class='' name='cosGradAttr[]' placeholder='Percent' value='0' readonly/>%</td>";
          echo $display;
        }
        ?>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>Overall Statement</td>
        <td id="mappingCourseAims" colspan="<?php echo sizeof($gradAttrList)+1 ?>"><?php if($function == 'update') echo $data['courseAims'];?></td>
        <td colspan="2"></td>
      </tr>
      <?php
      if($function == 'update'){
        foreach($data['learning_outcomes'] as $key => $LO){
          $display = "<tr id='rowMapLO".$key."'><td id='mapLO".$key."'>".
            $key.". ".$LO.
            "</td><td id='mapLOgradAttr' colspan='".(sizeof($gradAttrList)+1)."'>";
          $display.= "<span class='gradAttrInfo'>Hover mouse over text for more info.</span><br/><br/>";
          $bMapping = false;
          if(!empty($data['mapCosLOGradAttr'][$key])){
            $bMapping = true;
            $mapping = explode(", ", $data['mapCosLOGradAttr'][$key]);
          }
          foreach($gradAttrList as $gradAttrKey => $gradAttrContent){
            if($bMapping && in_array($gradAttrContent['ID'], $mapping)) $checked = "checked";
            else $checked = "";
            $display.= "<label class='cbGradAttrLbl'><input type='checkbox' id='".$key.$gradAttrContent['ID']."' class='cbGradAttr' name='LOgradAttr".$key."[]' value='".$gradAttrContent['ID']."' ".$checked."/><span title='".$gradAttrContent['main']."&#13;&#10;".$gradAttrContent['description']."'>".$gradAttrContent['ID']."</span></label>";
          }
          $display.= "</td><td colspan='2'>".
            "</td></tr>";
          echo $display;
        }
      }
      ?>
    </table>
  </td></tr>
</table>
<script>
$(document).ready(function(){
  //Display Learning outcomes (check if it is update)
  var bUpdate = false;
  var query = window.location.search.substring(1).split("&");
  for(var i = 0; i < query.length; i++){
    var pair = query[i].split("=");
    if(pair[0] == 'function' && pair[1] == 'update') bUpdate = true;
  }
  if(bUpdate){
    calculatePercentage();
  }
  else{
    var numLO = $('#LO tr').length;
    for(var c = 1; c <= numLO; c++){
      $('#MapGraduateAttributes').append('<tr id="rowMapLO'+c+'"><td id="mapLO'+c+'">'+c+'. </td><td id="mapLOgradAttr" colspan="<?php echo sizeof($gradAttrList)+1 ?>"></td><td colspan="2"></td></tr>');
    };

    // Display checkbox for Graduate Attribute list
    var gradAttrIDArr = [];
    jQuery.ajax({
      type:"POST",
      url:"includes/display.inc.php",
      datatype:"json",
      data:{type:'gradAttr'},

      success: function (obj){
        obj.forEach(function(item, index){
          gradAttrIDArr.push(item['ID']);
        });
        for (var i = 1; i <= numLO; i++){
          var gradAttrCheckbox = '<span class="gradAttrInfo">Hover mouse over text for more info.</span><br/><br/>';
          obj.forEach(function(item, index){
            gradAttrCheckbox += '<label class="cbGradAttrLbl"><input type="checkbox" id="'+i+item['ID']+'" class="cbGradAttr" name="LOgradAttr'+i+'[]" value="'+item['ID']+'"><span title="'+item['main']+'&#13;&#10;'+item['description']+'">'+item['ID']+'</span></label>';
          });
          $('#rowMapLO'+i+' #mapLOgradAttr').append(gradAttrCheckbox);
        }
      }
    });
  };

  function updateCourseLabel(){
    var courseCode = document.getElementById("courseCode").value;
    var courseTitle = document.getElementById("courseTitle").value;
    document.getElementById("courseLabel").innerHTML = courseCode + ' ' + courseTitle;
  }
  $(document).on('change', '#courseCode', function(){
    updateCourseLabel();
  });
  $(document).on('change', '#courseTitle', function(){
    updateCourseLabel();
  });
  $(document).on('change', '#courseAims', function(){
    document.getElementById("mappingCourseAims").innerHTML = document.getElementById("courseAims").value;
  });
  $(document).on('change', '#LO', function(){
    var numLO = $('#LO tr').length;
    for(var c = 1; c <= numLO; c++){
      document.getElementById("mapLO"+c).innerHTML = c + '. ' + document.getElementById("iLO"+c.toString()).value;
    }
  });

  function calculatePercentage(){
    var gradAttrIDArr = [];
    jQuery.ajax({
      type:"POST",
      url:"includes/display.inc.php",
      datatype:"json",
      data:{type:'gradAttr'},

      success: function (obj){
        obj.forEach(function(item, index){
          gradAttrIDArr.push(item['ID']);
        });
        var numLO = $('#LO tr').length;
        var gradAttrCountArr = [];
        for(var ga = 0; ga < gradAttrIDArr.length; ga++){
          gradAttrCountArr.push(0);
        }

        for(var i = 1; i <= numLO; i++){
          for(var ga = 0; ga < gradAttrIDArr.length; ga++){
            if(document.getElementById(i+gradAttrIDArr[ga]).checked == true)
              gradAttrCountArr[ga]++;
          }
        }
        for(var ga = 0; ga < gradAttrCountArr.length; ga++){
            document.getElementById("gradATTR"+gradAttrIDArr[ga]).value = (gradAttrCountArr[ga]*100/numLO).toString();
        }
      }
    });
  };
  $(document).on('change', '#MapGraduateAttributes', function(){
    calculatePercentage();
  })

  function updateLO(){
    var gradAttrArr = [];
    jQuery.ajax({
      type:"POST",
      url:"includes/display.inc.php",
      datatype:"json",
      data:{type:'gradAttr'},

      success: function (obj){
        obj.forEach(function(item, index){
          gradAttrArr.push(item);
        });
        var numLO = $('#LO tr').length;
        var numMapLORow = $('#MapGraduateAttributes tr').length - 4;
        if(numLO > numMapLORow){
          for(numMapLORow; numMapLORow < numLO; numMapLORow++){
            $('#MapGraduateAttributes').append("<tr id='rowMapLO"+(numMapLORow+1)+"'><td id='mapLO"+(numMapLORow+1)+"'>"+(numMapLORow+1)+". </td><td id='mapLOgradAttr' colspan='<?php echo sizeof($gradAttrList)+1 ?>'></td><td colspan='2'></td></tr>");
            var gradAttrCheckbox = '<span class="gradAttrInfo">Hover mouse over text for more info.</span><br/><br/>';
            gradAttrArr.forEach(function(item, index){
              gradAttrCheckbox += '<label class="cbGradAttrLbl"><input type="checkbox" id="'+(numMapLORow+1)+item['ID']+'" class="cbGradAttr" name="LOgradAttr'+(numMapLORow+1)+'[]" value="'+item['ID']+'"><span title="'+item['main']+'&#13;&#10;'+item['description']+'">'+item['ID']+'</span></label>';
            });
            $('#rowMapLO'+(numMapLORow+1)+' #mapLOgradAttr').append(gradAttrCheckbox);
          }
        }
      }
    });
  }

  //Update Course LO tested checkbox if any new Course Learning Outcome is added.
  $('#loadd').click(function(){
    updateLO();
  });

  $(document).on('click', '.LO_remove', function(){
    var button_id = $(this).attr("id");
    $('#rowMapLO'+button_id).remove();
    updateLO();
  });
});
</script>
