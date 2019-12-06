<form id='searchForm' method="post" action="search.php">
  <div id='submit'>
    <input type='hidden' name='function' value='search'/>
    <button type='submit' id='submit' name='submit'>Search Curriculum</button></td></tr>
  </div>

  <table id="searchTable" class='form'>
    <!-- <tr>
      <td colspan="2"><button class="collapsible" type="button">Course Main Details</button></td>
    </tr> -->
    <tr class="search">
      <td class="firstColumn">
        <label for="searchCode">Course Code:</label>
        <input id="searchCode" type="text" name="code" placeholder="Code">
      </td>
      <td  class="secondColumn">
        <label for="searchTitle">Course Title:</label>
        <input id="searchTitle" type="text" name="title" placeholder="Title">
      </td>
    </tr>
    <tr class="search">
      <td id='searchContactHours' rowspan="3" class="firstColumn">
        <span class='title'>Contact Hours:</span>
        <table>
          <tr>
            <td><label><input type='checkbox' name='contactType[]' value='lecture'/><span class='title'>Lecture</span></label></td>
            <td><input id='CHlec' type="number" name='contactHours[lecture]' min='1' max='50' disabled='disabled' placeholder="hours"></td></tr>
          <tr><td><label><input type='checkbox' name='contactType[]' value='tel'/><span class='title'>TEL</span></label></td>
            <td><input id='CHtel' type="number" name='contactHours[tel]' min='1' max='50' disabled='disabled' placeholder="hours"></td></tr>
          <tr><td><label><input type='checkbox' name='contactType[]' value='tutorial'/><span class='title'>Tutorial</span></label></td>
            <td><input id='CHtut' type="number" name='contactHours[tutorial]' min='1' max='50' disabled='disabled' placeholder="hours"></td></tr>
          <tr><td><label><input type='checkbox' name='contactType[]' value='lab'/><span class='title'>Lab</span></label></td>
            <td><input id='CHlab' type="number" name='contactHours[lab]' min='1' max='50' disabled='disabled' placeholder="hours"></td></tr>
          <tr><td><label><input type='checkbox' name='contactType[]' value='exampleclass'/><span class='title'>Example Class</span></label></td>
            <td><input id='CHexc' type="number" name='contactHours[exampleclass]' min='1' max='50' disabled='disabled' placeholder="hours"></td></tr>
        </table>
      </td>
      <td class="secondColumn">
        <label for='searchPrerequisite'>Pre-requisite:<input type="checkbox" name="coursePrerequisiteFor" value="True"/>For</label>
        <input id="searchPrerequisite" type="text" name='coursePrerequisite' placeholder="Code/Title">
      </td>
    </tr>
    <tr>
      <td class="secondColumn">
        <label for='searchAssessment'>Assessment:</label>
        <input id='searchAssessment' type="text" name='courseAssessment' placeholder="Assessment">
      </td>
    </tr>
    <tr>
      <td class="secondColumn">
        <label for="searchInstructor">Course Instructor:</label>
        <input id="searchInstructor" type="text" name='courseInstructor' placeholder="Name/Offce/Phone/Email">
      </td>
    </tr>
    <tr style="display:none">
      <td id='searchCosGradAttr' class="firstColumn">
        <label><span class='title'>Course Graduate Attributes: (%)</span></label><br>
        <?php
        $curr = new Curriculum();
        foreach(json_decode($curr->getGraduateAttribute(), true) as $gradAttr){
          $GA[$gradAttr['ID']]= $gradAttr['main'].'&#13;&#10;'.$gradAttr['description'];
        }
        ?>
        <table>
          <tr>
            <td colspan="6"><span class='gradAttrInfo'>Hover mouse over text for more info.</span></td>
          </tr>
          <tr>
            <td><label for='cosGradAttA' <?php echo 'title="'.$GA['a'].'"' ?>><u>A</u>:</label></td>
            <td><input id='cosGradAttA' type="number" name='cosGradAtt[a]' min='1' max='100'></td>
            <td><label for='cosGradAttE' <?php echo 'title="'.$GA['e'].'"' ?>><u>E</u>:</label></td>
            <td><input id='cosGradAttE' type="number" name='cosGradAtt[e]' min='1' max='100'></td>
            <td><label for='cosGradAttI' <?php echo 'title="'.$GA['i'].'"' ?>><u>I</u>:</label></td>
            <td><input id='cosGradAttI' type="number" name='cosGradAtt[i]' min='1' max='100'></td>
          </tr>
          <tr>
            <td><label for='cosGradAttB' <?php echo 'title="'.$GA['b'].'"' ?>><u>B</u>:</label></td>
            <td><input id='cosGradAttB' type="number" name='cosGradAtt[b]' min='1' max='100'></td>
            <td><label for='cosGradAttF' <?php echo 'title="'.$GA['f'].'"' ?>><u>F</u>:</label></td>
            <td><input id='cosGradAttF' type="number" name='cosGradAtt[f]' min='1' max='100'></td>
            <td><label for='cosGradAttJ' <?php echo 'title="'.$GA['j'].'"' ?>><u>J</u>:</label></td>
            <td><input id='cosGradAttJ' type="number" name='cosGradAtt[j]' min='1' max='100'></td>

          </tr>
          <tr>
            <td><label for='cosGradAttC' <?php echo 'title="'.$GA['c'].'"' ?>><u>C</u>:</label></td>
            <td><input id='cosGradAttC' type="number" name='cosGradAtt[c]' min='1' max='100'></td>
            <td><label for='cosGradAttG' <?php echo 'title="'.$GA['g'].'"' ?>><u>G</u>:</label></td>
            <td><input id='cosGradAttG' type="number" name='cosGradAtt[g]' min='1' max='100'></td>
            <td><label for='cosGradAttK' <?php echo 'title="'.$GA['k'].'"' ?>><u>K</u>:</label></td>
            <td><input id='cosGradAttK' type="number" name='cosGradAtt[k]' min='1' max='100'></td>
          </tr>
          <tr>
            <td><label for='cosGradAttD' <?php echo 'title="'.$GA['d'].'"' ?>><u>D</u>:</label></td>
            <td><input id='cosGradAttD' type="number" name='cosGradAtt[d]' min='1' max='100'></td>
            <td><label for='cosGradAttH' <?php echo 'title="'.$GA['h'].'"' ?>><u>H</u>:</label></td>
            <td><input id='cosGradAttH' type="number" name='cosGradAtt[h]' min='1' max='100'></td>
            <td><label for='cosGradAttL' <?php echo 'title="'.$GA['l'].'"' ?>><u>L</u>:</label></td>
            <td><input id='cosGradAttL' type="number" name='cosGradAtt[l]' min='1' max='100'></td>
          </tr>
        </table>
      </td>
    </tr>

  </table>
</form>
<script>
$(document).ready(function(){
  var cbChecked = [];
  $("#searchContactHours input:checkbox").click(function(){
    switch ($(this).prop('value')) {
      case 'lecture': var ID = '#CHlec'; break;
      case 'tel': var ID = '#CHtel'; break;
      case 'tutorial': var ID = '#CHtut'; break;
      case 'lab': var ID = '#CHlab'; break;
      case 'exampleclass': var ID = '#CHexc'; break;
    }
    if($(this).is(":checked") && $("#searchContactHours input:checked").length < 4){
      $(ID).removeAttr("disabled");
      $(ID).focus();
      cbChecked.push(ID);
    } else {
      $(ID).attr("disabled", 'disabled');
      for(var i = 0; i < cbChecked.length; i++){
        if(cbChecked[i] == ID){
          cbChecked.splice(i, 1);
        }
      }
    }
    if($("#searchContactHours input:checked").length > 3){
      $(this).prop('checked', false);
    }
  });

  var coll = document.getElementsByClassName("collapsible");
  var i;

  for (i = 0; i < coll.length; i++) {
    coll[i].addEventListener("click", function() {
      this.classList.toggle("active");
      var nextRow = this.parentNode.parentNode.nextElementSibling;
      var nextNextRow = this.parentNode.parentNode.parentNode.childNodes[3].nextElementSibling;
      if (nextRow.style.display === "table-row" && nextNextRow.style.display === "table-row") {
        nextRow.style.display = "none";
        nextNextRow.style.display = "none";
      } else {
        nextRow.style.display = "table-row";
        nextNextRow.style.display = "table-row";
      }
    });
  }
});
</script>
