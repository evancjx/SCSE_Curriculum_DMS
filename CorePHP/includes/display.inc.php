<?php
include 'dbh.inc.php';
include 'curriculum.inc.php';
include_once "filter.inc.php";

header('Content-Type: application/json');

$c = new Curriculum();

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  switch($_POST['type']){
    case 'gradAttr':
      echo $c->getGraduateAttribute();
      break;
    case 'cosGradAttrPercent':
      echo $c->getCosGradAttrPercent($_POST['courseCode']);
      break;
    case 'LO':
      echo $c->getLO($_POST['courseCode']);
      break;
    case 'mapCosGradAttr':
      echo $c->getMapCosGradAttr($_POST['courseCode']);
      break;
    case 'scheduleLO':
      echo $c->getScheduleLO($_POST['courseCode']);
      break;
    case 'cosLOTested':
      echo $c->getCosLOTested($_POST['courseCode']);
      break;
  }
}
else if ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET['term'])){
  echo $c->getPrerequitiesLike(Filter::String($_GET['term']));
}

?>
