<table id="tblAppendix" class='table'>
  <tr><td class='label bbl'>Appendix</td></tr>
  <tr><td class='btl'>
    <table id="Appendix" class="subtable">
      <?php
      if($function == 'update' && !empty($data['appendix'])){
        foreach($data['appendix'] as $Akey => $item){
          $display = "<tr id='appendixHeader".$Akey."' class='appendix tbt'><td>".
            "Appendix ".$Akey.": <input type='text' class='appendixHeader' name='appendixHeader[]' placeholder='Appendix Header' value='".$item['header']."'/>".
            "</td><td class='action'>";
          if($Akey == 1)
            $display.= "<input type='button' name='add' id='appendixAdd' value='Add'/>";
          else
            $display.= "<input type='button' name='remove' id='".$Akey."' class='btn btn-danger appendixRemove' value='Del'>";
          $display.= "</td></tr>".
            "<tr id='appendixDescription".$Akey."' class='description'><td>".
            "<textarea type='text' name='appendixDescription[]' placeholder='Description'>".$item['description']."</textarea>".
            "</td><td class='action'>".
            "<input type='button' name='add' id='".$Akey."' class='appendixCriteriaTableAdd' value='Add'/>Criteria Table".
            "</td></tr>";
          echo $display;
          $bTableHeader = false;
          $bTableFooter = true;
          foreach($data['criteria'] as $Ckey => $criteriaItem){
            if($Akey != $criteriaItem['appendixID']) continue;
            if(!$bTableHeader){
              $bTableHeader = true;
              echo "<tr id='appendixCriteriaTable".$Akey."'><td colspan='2' class='bll brl bbl'>".
                "<table id='criteria".$Akey."' class='criteriaTable'>".
                "<tr><td rowspan='2'>".
                "Criteria\nfor Appendix ".$Akey.
                "</td><td colspan='3'>".
                "Standards".
                "</td><td class='action' rowspan='2'>".
                "<input type='button' name='add' id='".$Akey."' class='appendixCriteriaTableDel' value='Del'/>Criteria Table".
                "</td></tr>" .
                "<tr><td>" .
                "Fail Standard (0-39%)" .
                "</td><td>" .
                "Pass Standard (40-80%)" .
                "</td><td>" .
                "High Standard (81-100%)" .
                "</td></tr>";
                $bTableFooter = false;
            }
            echo "<tr id='criteriaTableRow1".$Ckey."' class='criteriaRow'><td>" .
              "<textarea type='text' name='assessmentCriteria".$Akey."[]' placeholder='Assessment'>".$criteriaItem['header']."</textarea>" .
              "</td><td>" .
              "<textarea type='text' name='assessmentFail".$Akey."[]' placeholder='Fail Standards'>".$criteriaItem['fail']."</textarea>" .
              "</td><td>" .
              "<textarea type='text' name='assessmentPass".$Akey."[]' placeholder='Pass Standards'>".$criteriaItem['pass']."</textarea>" .
              "</td><td>" .
              "<textarea type='text' name='assessmentHigh".$Akey."[]' placeholder='High Standards'>".$criteriaItem['high']."</textarea>" .
              "</td><td>" .
              "<input type='button' name='add' id='".$Akey."' class='criteriaTableAdd' value='Add'/>" .
              "</td></tr>";
          }
          if(!$bTableFooter){
            $bTableFooter = true;
            echo "</table></td></tr>";
          }
        }
      }
      else{
        echo "<tr id='appendixHeader1' class='appendix tbt'><td>".
          "Appendix 1: <input type='text' class='appendixHeader' name='appendixHeader[]' placeholder='Appendix Header'/>".
          "</td><td class='action'>".
          "<input type='button' name='add' id='appendixAdd' value='Add'/>".
          "</td></tr>".
          "<tr id='appendixDescription1' class='description'><td>".
          "<textarea type='text' name='appendixDescription[]' placeholder='Description'></textarea>".
          "</td><td class='action'>".
          "<input type='button' name='add' id='1' class='appendixCriteriaTableAdd' value='Add'/>Criteria Table".
          "</td></tr>";
      }
      ?>
    </table>
  </td></tr>
</table>
<script>
$(document).ready(function(){
  $('#appendixAdd').click(function(){
    var numAppendix =  $('#Appendix .appendix').length + 1;
    var AppendixRow = "<tr id='appendixHeader"+numAppendix+"' class='appendix tbt'><td>" +
      "Appendix "+numAppendix+": <input type='text' class='appendixHeader' name='appendixHeader[]' placeholder='Appendix Header'/>" +
      "</td><td class='action'>" +
      "<input type='button' name='remove' id='"+numAppendix+"' class='btn btn-danger appendixRemove' value='Del'>" +
      "</td></tr>" +
      "<tr id='appendixDescription"+numAppendix+"'><td>" +
      "<textarea type='text' name='appendixDescription[]' placeholder='Description'></textarea>" +
      "</td><td class='action'>" +
      "<input type='button' name='add' id='"+numAppendix+"' class='appendixCriteriaTableAdd' value='Add'/>Criteria Table" +
      "</td></tr>";
    $('#Appendix').append(AppendixRow);
  });
  $(document).on('click', '.appendixRemove', function(){
    var button_id = $(this).attr("id");
    $('#appendixHeader'+button_id+'').remove();
    $('#appendixDescription'+button_id+'').remove();
    if(document.getElementById('appendixCriteriaTable'+button_id) != null)
      $('#appendixCriteriaTable'+button_id+'').remove();
  });
  $(document).on('click', '.appendixCriteriaTableAdd', function(){
    var button_id = $(this).attr("id");
    if(document.getElementById('appendixCriteriaTable'+button_id) == null) {
      var criteriaTable = "<tr id='appendixCriteriaTable"+button_id+"'><td colspan='2' class='bll brl bbl'>" +
        "<table id='criteria"+button_id+"' class='criteriaTable'>" +
        "<tr><td rowspan='2'>" +
        "Criteria\nfor Appendix " + button_id +
        "</td><td colspan='3'>" +
        "Standards" +
        "</td><td class='action' rowspan='2'>" +
        "<input type='button' name='add' id='"+button_id+"' class='appendixCriteriaTableDel' value='Del'/>Criteria Table" +
        "</td></tr>" +
        "<tr><td>" +
        "Fail Standard\n(0-39%)" +
        "</td><td>" +
        "Pass Standard\n(40-80%)" +
        "</td><td>" +
        "High Standard\n(81-100%)" +
        "</td></tr>" +
        "<tr id='criteriaTableRow11' class='criteriaRow'><td>" +
        "<textarea type='text' name='assessmentCriteria"+button_id+"[]' placeholder='Assessment'></textarea>" +
        "</td><td>" +
        "<textarea type='text' name='assessmentFail"+button_id+"[]' placeholder='Fail Standards'></textarea>" +
        "</td><td>" +
        "<textarea type='text' name='assessmentPass"+button_id+"[]' placeholder='Pass Standards'></textarea>" +
        "</td><td>" +
        "<textarea type='text' name='assessmentHigh"+button_id+"[]' placeholder='High Standards'></textarea>" +
        "</td><td>" +
        "<input type='button' name='add' id='"+button_id+"' class='criteriaTableAdd' value='Add'/>" +
        "</td></tr>" +
        "</table>" +
        "</td></tr>";
      $('#appendixDescription'+button_id+'').after(criteriaTable);
    }
  });
  $(document).on('click', '.appendixCriteriaTableDel', function(){
    var button_id = $(this).attr("id");
    $('#appendixCriteriaTable'+button_id+'').remove();
  });
  $(document).on('click', '.criteriaTableAdd', function(){
    var button_id = $(this).attr("id");
    var numCriteria =  $('#criteria'+button_id+' .criteriaRow').length + 1;
    var criteriaTableRow = "<tr id='criteriaTableRow"+button_id+""+numCriteria+"' class='criteriaRow'><td>" +
      "<textarea type='text' name='assessmentCriteria"+button_id+"[]' placeholder='Assessment'></textarea>" +
      "</td><td>" +
      "<textarea type='text' name='assessmentFail"+button_id+"[]' placeholder='Fail Standards'></textarea>" +
      "</td><td>" +
      "<textarea type='text' name='assessmentPass"+button_id+"[]' placeholder='Pass Standards'></textarea>" +
      "</td><td>" +
      "<textarea type='text' name='assessmentHigh"+button_id+"[]' placeholder='High Standards'></textarea>" +
      "</td><td>" +
      "<input type='button' name='add' id='"+button_id+""+numCriteria+"' class='criteriaTableDel' value='Del'/>" +
      "</td></tr>";
    $('#criteria'+button_id+'').append(criteriaTableRow);
  });
  $(document).on('click', '.criteriaTableDel', function(){
    var button_id = $(this).attr("id");
    $('#criteriaTableRow'+button_id+'').remove();
  });
});
</script>
