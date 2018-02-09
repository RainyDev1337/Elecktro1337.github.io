<?
// символы "<?" должны быть первыми(!) в файле. Т.е. ни пустых строк, ни
// пробелов до символов "<?" ставить нельзя, иначе не будет работать
// header - появятся warning'и

unset($t);
session_start();
include("data/config_shop.php"); //подключаем файл конфигурации

// проверка в каком подразделе находимся, 
// если не в подразделе - идем на главную
if(!$_GET[sub]){$sub="shop";} 


/*
   функция прибавляет в корзину новый товар, где $n - это номер строки
   в shop.txt. Далее, в сессиях сохраняется не номер строки, а число ID
   из shop.txt и используется повсеместно. Если товар уже существует,
   то корзина никак не меняется.
*/

function tadd($n,$sub) {
   global $t, $sub;

   // открыли файл
   $f=file("data/shop/$sub.txt") or die(header("Location: $PHP_SELF?c=error"));
   // и получили нужную строку с товаром (в массив $o)
   $o=explode("|",$f[$n]);

   $id=$o[0];
   if (isset($t[all][$id])) return; // если товар уже в корзине - выход

   $t[all][$id]=$id;    // флаг, благодаря которому, двумя строчками выше
                        // мы определили, что товар уже есть в корзине
   $t[$id][name]=$o[2]; // наименование
   $t[$id][info]=$o[3]; // инфо кратко
   $t[$id][cena]=$o[1]; // цена
   $t[$id][kol]=1;      // кол-во в начале равно "1 штуке"

   session_register("t"); // записали переменную в сессию
}


/*
   Рисует таблицу с товарами в корзине. Из файла shop.txt мы читаем только
   названия колонок и не более. Названия товара (в данном случае только
   название/цена/кол-во) берется из сессии.
*/
function korzina() {
   global $t,$PHP_SELF,$SID,$config_use_valut;

   $ogl=explode("|",$f[0]);

   echo "<form action=$PHP_SELF? method=POST>".
        "<input type=hidden name=c value=kolvo>".
   // рисуем заголовок таблицы с корзиной:
        "<table id=basket><tr><td class=basket_head>Цена, $config_use_valut</td><td class=basket_head>Наименование</td><td class=basket_head>кол-во</td><td class=basket_head>команды</td></tr>";
   // проходим массив $t[all] по списку его ключей
  if(count($t)==1 or $t==""){echo "<tr><td class=basket_td align=center colspan=4>Ваша корзина пуста...</td></tr>";}
  else{$k=@array_keys($t[all]);
     for ($i=0; $i<count($k); $i++) { //>
      $id=$k[$i];
      echo "<tr><td class=basket_td>{$t[$id][cena]}</td><td class=basket_td >{$t[$id][name]}</td>".
           "<td class=basket_td><input size=4 type=text name=v[$id] value={$t[$id][kol]}></td>".
           "<td class=basket_td><a href=$PHP_SELF?c=del&id=$id>удалить</a></td></tr>";
   }
}
   // внизу таблицы две кнопки:
   //   Измениения - сохранить изменение числа товаров и обновить страницу
   //   Заказ - сорх. изм. + перейти на страницу оформления заказа
   if(count($t)==1 or $t==""){$disabled="disabled";}
   echo "<tr><td colspan=2><input type=submit name=edit value='Внести изменения' $disabled><input type=submit name=zakaz value='Оформить заказ' $disabled></td>
         <td colspan=2 align=right><a href='$PHP_SELF?c=delete'><input type=button value='Очистить корзину' $disabled></a></td></tr></table></form>";
}
//////////////////////////////////////////////////////////////////////////////////
/*
   Выводит на экран таблицу с товарами.
*/
function price() {
include("data/config_shop.php");
   global $t, $PHP_SELF, $sub;
   $f=file("data/shop/$sub.txt") or die(header("Location: $PHP_SELF?c=error")); // читаем файл
    $ogl=explode("|",$f[0]);
    $x=(count($ogl)-1); // вычисляем число подразделов? за вычетом описания
   $y=count($f);   // и число строк с описанием товара
   $num_p=ceil(($y-1)/$e);

if ($sub!="shop" & !$_GET[shf]){
echo "<form action=$PHP_SELF method=POST>
      <input type=hidden name=c value=add>
  	  <table width='100%' height='95%' border='0'><tr><td colspan='$colomn'>";

//вывод списка подкатегорий из первой строки
echo "<table width='100%' border='0'><tr><td width='60%'>$ogl[0] &nbsp;</td><td>"; // вывод описания раздела
   for ($i=1; $i<=$x; $i++) {
    $subr=explode("::",$ogl[$i]);
    echo"<a href='?sub=$subr[1]'>$subr[0]</a> <br>";            // вывод подразделов
   }
echo "</td></tr></table></td></tr><tr>";

$d=intval($_GET['d']);
if($d<=0 or $d>($y-1)){$d=1;}
$b=($d+$e);
   for ($i=$d; $i<$b; $i++) {
      $a=explode("|",$f[$i]); // читаем очередную строку файла
      if (count($a)<2) continue; // если она пустая (глюки), пропускаем
      echo "<td><table border='0'>";
      // цикл вывода всех колонок текущей строки таблицы
      for ($j=0; $j<1; $j++) { if($a[5]!=''){$a[5]="<tr><td align=center><a title='".$a[2]."' href='?shf=".($i+1)."&sub=".$sub."'><img alt=".$a[2]." border='0' src=".$a[5]."></a></td></tr>";}
      							if($a[2]!=''){$a[2]="<tr><td align=center><b><a title='Подробнее' href='?shf=".($i+1)."&sub=".$sub."'>".$a[2]."</a></b></td></tr>";}
      							if($a[3]!=''){$a[3]="<tr><td>".$a[3]."</td></tr>";}
      							if($a[1]!=''){$a[1]="<tr><td>Цена: $a[1] $config_use_valut</td></tr>";}
          echo "$a[5]";
          echo "$a[2]";
          echo "$a[3]";
          echo "$a[1]";
          }

echo "</table><a title='Добавить в корзину' href='?c=add&v[$i]=$i&sub=$sub'><img border='0' src='skins/images/cart_navy.gif' alt='Добавить в корзину'></a></td>";

                if ($i%$colomn==0){echo "</tr><tr>";}
   }
   echo "<tr  valign='bottom'><td height='100%' colspan='$colomn'>";
   // Номера страниц забацаем
   if($num_p!=0 & $num_p!=1){print"Страницы: ";
    for ($l=1; $l<=$num_p; $l++) {
     $num_p_t=floor($b/$e);
     $num_p_s=($e*($l-1)+1);
     if($l==$num_p_t){print"[<b>$l</b>]";}else{print"[<a href='?d=$num_p_s&sub=".$sub."'>$l</a>]";}
    }
   }
   echo "</td></tr></table></form>";
}elseif ($_GET[shf]){
  echo "<form action=$PHP_SELF method=POST><input type=hidden name=c value=add>
  		<table width='100%' border='0'><tr>
	    <td><table border='0' width='100%'>"; 
	    $i=($_GET[shf]-1);
	    if($i>$y or $i<1){exit(header("Location: $PHP_SELF?"));}//проверка существует ли такой товар
	$a=explode("|",$f[$i]); // читаем нужную строку файла для вывода подробного описания
	if($a[5]!=''){$a[5]="<tr><td><img alt=".$a[2]." border='0' src=".$a[5]."></td></tr>";}
	if($a[4]!=''){$a[4]="<tr><td>".$a[4]."</td></tr>";}else{$a[4]="<tr><td>".$a[3]."</td></tr>";}
	if($a[1]!=''){$a[1]="<tr><td>Цена: $a[1] $config_use_valut</td></tr>";}
          echo "<tr><td><b>$a[2]</b></td></tr>";
          echo "$a[5]";
          echo "$a[4]";
          echo "$a[1]";
          echo "</table><a title='Добавить в корзину' href='?c=add&v[$i]=$i&sub=$sub'><img border='0' src='skins/images/cart_navy.gif' alt='Добавить в корзину'></a>";
   echo "</td></tr></table></form>";
 }else{  //вывод главной страницы магазина
   echo"<table border='0' width='100%'><tr>";
    for ($i=1; $i<=$x; $i++) {
    $subr=explode("::",$ogl[$i]); if(trim($subr[2])==''){$subr[2]='noimg.jpg';}else{$subr[2]=trim($subr[2]);}
    echo"<td align='center' valign='bottom'><a href='?sub=$subr[1]'><img alt='$subr[0]' border='0' src='data/upimages/$subr[2]'><br><h1>$subr[0]</h1></a><br>"; // вывод разделов
      // выводим подразделы
      if(file_exists("data/shop/$subr[1].txt")){
       $sub_f=@file("data/shop/$subr[1].txt"); // читаем файл
       $sub_ogl=@explode("|",$sub_f[0]);
       $sub_x=(@count($sub_ogl)-1); // вычисляем число подразделов? за вычетом описания
        for ($sub_i=1; $sub_i<=$sub_x; $sub_i++) {
         $sub_subr=@explode("::",$sub_ogl[$sub_i]);
         echo"<a href='?sub=$sub_subr[1]'>$sub_subr[0]</a><br>"; // вывод подразделов
        }
      }
    echo"</td>";
    if(!($i%2)){echo"</tr><tr>";}
    }
   echo"</tr></table>";
   echo "<li><a href='$PHP_SELF?do=shop&c=korzina'>Корзина покупок</a>";
 }
}

/*
   Выводит на экран несколько чисел (написано). Подсчет значений происходит
   при каджом вызове.
*/
function summa() {
   global $t, $config_use_valut;
   // традиционный проход массива товаров из корзины
   $k=@array_keys($t[all]);
   for ($i=0; $i<count($k); $i++) {  //>
      $id=$k[$i];
      // если убрать (double), то копейки округляться
      $summ+=(double)$t[$id][kol]*(double)$t[$id][cena];
      $summ2+=$t[$id][kol];
   }
   if($summ2==""){$summ2="0";}
   // просто выводим посчитанные цифры на экран
   echo "<a href='$PHP_SELF?do=shop&c=korzina'>Корзина</a>: наименований товаров - $i (в кол-ве  $summ2 шт.), цена -  ".sprintf("%.2f$config_use_valut",$summ);
}

/*
   Объявление переменной post, которая содержит поля для заполнения
   посетителем при оформление заказа. 
*/
   $post=file("data/polia.php");


/*****************************************************************************/
// основной код программы

// $c - основная переменная, указывающая на нужное действие
if (!isset($c)) $c='';

switch($c) {

case "":
// без параметров - рисуем прайс-лист

   summa();// статистика по корзине
   price(); // прайс
   break;


case "error":
// вывод страницы ошибки
   echo "<h2>Ошибка</h2>";
	echo "<p align='center'>Извините, но вы запросили несуществующую страницу...</p>";
   // пишем 1 ссылку
   echo "<li><a href='javascript:history.back(1);'>Вернуться к покупкам</a>";
   break;



case "korzina":
// вывод корзины
   echo "<h2>Корзина покупок</h2>";
   summa(); // см. выше
   korzina(); // рисуем таблицу корзины
   // пишем 1 ссылку
   echo "<li><a href='javascript:history.back(1);'>Вернуться к покупкам</a>";
   break;


case "add":
// добавление из формы прайса товара

   // в массиве $v скоплены номера строк товаров, которые функция ...
  $f=file("data/shop/$sub.txt") or die(header("Location: $PHP_SELF?c=error")); // читаем файл
  $y=count($f);   // число строк
   $k=@array_keys($v);
   for ($i=0; $i<count($k); $i++) {  //>
      // ... tadd() преобразует из файла в данные и поместит в сессии
      if($k[$i]>($y-1) or $k[$i]<1){exit(header("Location: $PHP_SELF?"));}//проверка существует ли такой товар
      tadd($v[$k[$i]], $sub);
   }
   // надо перенаправить браузер на приличный адрес, чтобы:
   // 1) в URL был написан приличный адрес
   // 2) чтобы не было глюка, если посетитель нажмет ОБНОВИТЬ СТРАНИЦУ
   exit(header("Location: $PHP_SELF?c=korzina"));
   // Ну, а то, что header засунуто в exit... Это просто фича такая :-)
   break;


case "kolvo":
// измение кол-ва товаров, когда а странице КОРЗИНА нажимают СОХРАНИТЬ
// ИЗМЕНЕНИЯ или ОФОРМИТЬ ЗАКАЗ..
// Оцените, насколько короткий код преобразования корзины

   $k=@array_keys($v);
   for ($i=0; $i<count($k); $i++) {  //>
      $t[$k[$i]][kol]=abs(intval($v[$k[$i]]));
   }
   // после изменения переенной сессии ее нужно записать
   session_register("t");

   // Далее важная проверка. Если посетитель нажимает кнопку СОХРАНИТЬ, то
   // у нас устанавливается переменная $edit, которая содержит строку
   // "Сохранить изменения". Если он нажимает ЗАКАЗ, то устанавливается
   // $post. Устанавливается только одна из этих твух переменных.

   // если это было ИЗМЕНИТЬ, то переправить на корзину
   if (isset($edit)) exit(header("Location: $PHP_SELF?c=korzina"));
   // иначе переправить на страницу с офрмлением заказа
   exit(header("Location: $PHP_SELF?c=zakaz"));
   break;

case "del":
// удаление товара по его $id

   $id=intval($id);
   unset($t[$id]);
   unset($t[all][$id]);
   session_register("t");
   exit(header("Location: $PHP_SELF?c=korzina"));
   break;


case "delete":
// удаление всей корзины.. Как и в пред. пункте, только с проходом
// массива id товаров

   $k=@array_keys($t[all]);
   for ($i=0; $i<count($k); $i++) {   //>
      unset($t[$k[$i]]);
      unset($t[all][$k[$i]]);
   }
   session_register("t");
   exit(header("Location: $PHP_SELF?c=korzina"));


case "zakaz":
// форма для оформления заказа

 $all_ways = @file("data/category_payment.txt");
  $count_payment = 0;
    foreach($all_ways as $ways_line){
    $ways_arr = explode("|", $ways_line);
    $ways_arr[1] = stripslashes( preg_replace(array("'\"'", "'\''"), array("&quot;", "&#039;"), $ways_arr[1]) );
    $ways_help_names[] = $way_arr[1];
    $ways_help_ids[] = $way_arr[0];
    $results .= "<tr><td valign=top width='6'><input name=sposob type=radio value='$ways_arr[1]' checked></td><td valign=top><b>$ways_arr[1]</b></td><td valign=top><p style='COLOR: #808080; border: 1px solid #C5C5C5;'> $ways_arr[2]</p></td></tr>";
	$count_payment ++;}

echo "<h2>Оформление заказа</h2>";

echo "<script> function check() {";
for ($i=1; $i<count($post); $i++) { print"p_sender = document.myform.v$i.value.toString();
		if(p_sender != '') { if(p_sender.length<3 || p_sender.length>50) {alert ('Ошибка в поле ".trim($post[$i])."!'); document.myform.v$i.focus();return false; }
		}
 else { alert('Заполните поле ".trim($post[$i])."!'); document.myform.v$i.focus(); return false; }"; 
}
echo "}</script>";
   echo "<form align=center name='myform' action=$PHP_SELF? method=post onSubmit='return check();'><input type=hidden name=c value=post>".
        "<input type=hidden name=SID value='$SID'>".
        "<table width=100% >";
   for ($i=1; $i<count($post); $i++) {    //>
      echo "<tr><td colspan=2><nobr>$post[$i]</nobr></td><td><input type=text size=40 name='v[$i]' id=v$i></td></tr>\n";
   }
   echo"$results";
   echo "</table><input type='submit' name='Submit' value='Отправить заказ'></form>";
   break;


case "post":
// генерим и отправляем анкету посетителя, где указаны данные посетителя

   for ($i=1; $i<count($post); $i++) {       //>
      $v[$i] = htmlspecialchars(stripslashes(trim($v[$i])));
      if($v[$i]==""){exit(header("Location: $PHP_SELF?c=korzina"));}
      $post[$i]=trim($post[$i]);
      $ank.="$post[$i]: ".substr($v[$i],0,50)."<br>";

   }
// и список товаров из корзины
  if(!$t[all]){echo"<h2><center><br>Ваша корзина пуста...</center></h2>"; continue;}
   $k=@array_keys($t[all]);
   for ($i=0; $i<count($k); $i++) {         //>
      $id=$k[$i];
      $msg.=($i+1).") {$t[$id][name]}  ".doubleval($t[$id][cena]) ."$config_use_valut  {$t[$id][kol]} шт.  = ".
           sprintf("%.2f",$t[$id][cena]*$t[$id][kol])." $config_use_valut \n";
   }
   $dost=$sposob;

	$fo = @fopen("data/zak.txt","a");
	$msg = ereg_replace("\r","",$msg);
	$msg = ereg_replace("\n","<br>",$msg);
	
	$date = date("d.m.Yг.,в G:i.");
    
	$string = "$date|$ank|$msg|$sposob|\r\n";
	fputs($fo,$string);
	fclose($fo);
session_unregister("t");	
if ($fo){echo"<h2>Ваш заказ принят<br>Спасибо за покупку!</h2>
				ждите: с вами свяжется наш сотрудник...<hr>
				<table width=100% ><tr><td>Анкета посетителя:<p> $ank </p>
										Список покупок:<p> $msg </p>
										Доставка: $sposob</td></tr>
				</table>";
}
}
?>