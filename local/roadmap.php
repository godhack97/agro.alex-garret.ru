<?
$text = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/local/roadmap.todo');

$re = '/^(.*✔)(.*)(@done.*)/m';
$str = $text;
$subst = '<div style="display: inline-flex;"><div style="color:green">$1$2</div><div style="color:#3c3e3c">$3</div></div>';
$result = preg_replace($re, $subst, $str);


echo '<pre style="font-weight: 490; font-size: 14px;">';
print_r($result);
echo '</pre>';

?>