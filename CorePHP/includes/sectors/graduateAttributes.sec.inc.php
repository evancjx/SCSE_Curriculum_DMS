<table id="tblGraduateAttributes" class="table">
  <tr><td>The graduate attributes as stipulated by the EAB, are:</td></tr>
  <tr><td>
    <table id="graduateAttributes" class="subtable">
      <?php
        foreach($gradAttrList as $key => $gradAttr){
          $display = "<tr><td class='bl'>(".$gradAttr['ID'].")</td>".
            "<td class='bl'><b>".$gradAttr['main']."</b>: ".$gradAttr['description']."</td>";
          echo $display;
        }
      ?>
    </table>
  </td></tr>
</table>
