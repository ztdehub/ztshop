<?php
// 1导出
//$array = array(
//    array("id" => 1, "name" => "食物", "pid" => 0),
//    array("id" => 2, "name" => "衣服", "pid" => 0),
//    array("id" => 3, "name" => "水果", "pid" => 1),
//    array("id" => 4, "name" => "蔬菜", "pid" => 1),
//    array("id" => 5, "name" => "男装", "pid" => 2),
//    array("id" => 6, "name" => "女装", "pid" => 2),
//    array("id" => 7, "name" => "苹果", "pid" => 3),
//    array("id" => 8, "name" => "葡萄", "pid" => 3),
//    array("id" => 9, "name" => "番茄", "pid" => 4),
//    array("id" => 10, "name" => "土豆", "pid" => 4),
//    array("id" => 11, "name" => "T恤", "pid" => 5),
//    array("id" => 12, "name" => "衬衫", "pid" => 5),
//    array("id" => 13, "name" => "连衣裙", "pid" => 6),
//    array("id" => 14, "name" => "短裤", "pid" => 6),
//);

//include 'Classes/PHPExcel.php';
//include 'Classes/PHPExcel/Writer/Excel2007.php';
//$objPHPExcel = new PHPExcel();
//
//$objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
//$objPHPExcel->getActiveSheet()->setCellValue('B1', 'NAME');
//for ($i = 0; $i < count($array); $i++) {
//    $k = $i + 2;
//    $objPHPExcel->getActiveSheet()->setCellValue('A' . $k, $array[$i]['id']);
//    $objPHPExcel->getActiveSheet()->setCellValue('B' . $k, $array[$i]['name']);
//}

//$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//$objWriter->save("a.xlsx");

// 2、导入

include 'Classes/PHPExcel.php';
$PHPExcel = new PHPExcel();
$filePath = "a.xlsx";
// 默认用excel2007读取excel，若格式不对，则用之前的版本进行读取
$PHPReader = new PHPExcel_Reader_Excel2007();
if(!$PHPReader->canRead($filePath)){
    $PHPReader = new PHPExcel_Reader_Excel5();
    if(!$PHPReader->canRead($filePath)){
        echo 'no Excel';
        return ;
    }
}
// 加载excel文件
$PHPExcel = $PHPReader->load($filePath);

// 读取excel文件中的第一个工作表
$currentSheet = $PHPExcel->getSheet(0);
// 取得最大的列号
$allColumn = $currentSheet->getHighestColumn();
// 取得一共有多少行
$allRow = $currentSheet->getHighestRow();

// 从第二行开始输出，因为excel表中第一行为列名
for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
    /**从第A列开始输出*/
    for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){
        $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
		 echo $val."\t";
        if($currentColumn == 'A')
        {
			$id=$val;
            echo $val."\t";
        }else{
			$name=$val;
            //如果输出汉字有乱码，则需将输出内容用iconv函数进行编码转换，如下将gb2312编码转为utf-8编码输出
            //echo iconv('utf-8','gb2312', $val)."\t";
			echo $val."\t";
        }
//		$sql="inset into user (`id`,`name`) values ('$id','$name')";
    }
    echo "</br>";
}
echo "\n";