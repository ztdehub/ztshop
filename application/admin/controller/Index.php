<?php
namespace app\admin\controller;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use think\Db;
class Index extends Common
{

    public function list()
    {

        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

            return;
        }
// Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

// Set document properties
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');

// Add some data
        $arr=Db::table('detail')->select();

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'id')
            ->setCellValue('B1', 'name')
            ->setCellValue('C1', 'num');

// Miscellaneous glyphs, UTF-8
        foreach ($arr as $key => $value){
            $i=$key+2;
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $value['id'])
                ->setCellValue('B'.$i, $value['name'])
                ->setCellValue('C'.$i, $value['num']);
        }

// Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xls)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="01simple.xls"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');


    }
    function excel(){
        $file = request()->file('file');
        $info = $file->validate(['size'=>15678,'ext'=>'xls'])->move( './excel');
        if($info){
            $getSaveName=str_replace("\\","/",$info->getSaveName());
            $helper = new Sample();
            $inputFileName = "./excel/".$getSaveName;
            $helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory to identify the format');
            $spreadsheet = IOFactory::load($inputFileName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
//        var_dump($sheetData);
            foreach ($sheetData as $key => $value){
                $data=['name'=>$value['B'],'num'=>$value['C']];
                Db::table('detail')->insert($data);
                var_dump($value);

            }
            unset($info);
            unlink('./excel/'.$getSaveName);
        }else{
            echo '文件类型不对';
        }
    }
    function index(){
        return $this->fetch();
    }
}
