<?Php
for ($i = 0; $i <= 1; $i++){
$file = file('https://www.indeed.com.mx/jobs?q=medio+tiempo&sort=date&start='.$i*10);
    foreach($file as $linenum => $line){
    	echo "<b>Line #{$linenum}</b> ".htmlspecialchars($line).'</br>';
	}
}
?>