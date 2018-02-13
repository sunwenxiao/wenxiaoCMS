<?php
defined('WENXIAOCMS') or exit('Access denied!');
/**
  *excel操作类
  *
  */
class excel{
	static function export($option = array()){
		//config 初始化
		$title 	= isset( $option['title'])?$option['title']:'Sheet1';
		$output = isset( $option['output'])?$option['output'].'.xls':'output'.date('YmdHis').'.xls';
		$data   = isset( $option['data'])?$option['data']:array();
		
		//开始
		include 'PHPExcel/PHPExcel.php';
		include 'PHPExcel/PHPExcel/Writer/Excel5.php';
		$objExcel = new PHPExcel();             
		$objWriter = new PHPExcel_Writer_Excel5($objExcel); 
		$objExcel->setActiveSheetIndex(0);
		$objActSheet = $objExcel->getActiveSheet();
		//设置当前活动sheet的名称
		$objActSheet->setTitle($title);
		$i = 1;
		$char ='A,B,C,D,E,F,G,H,I,J,K,L,M,O,P,Q,R,S,T,U,V,W,X,Y,Z,AA,AB,AC,AD,';
		$char.=	'AE,AF,AG,AH,AI,AJ,AK,AL,AM,AN,AO,AP,AQ,AR,AS,AT,AU,AV,AW,AX,AY,AZ';
		$chars= explode(',',$char);
		foreach($data as $row){
			$j = 0;
			foreach($row as $v){
				$index =$chars[$j].$i;
				if($i==1){
					$objActSheet->getColumnDimension($chars[$j])->setWidth(20); ;  
					//$objActSheet->getStyle($index)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);  
    				//$objActSheet->getStyle($index)->getFill()->getStartColor()->setARGB("FFFF0000");
				}
				$objActSheet->setCellValue($index, $v);	
				$j++;
			}
			$i++;
		}
		//输出内容
	   	$outputFileName = APPLICATION.'/caches/data/'.$output;
		//到文件
		$objWriter->save($outputFileName); 
		load_sys_func('extend');
		file_down($outputFileName,$output,1);
		/*		
		//供下载
		header("Content-Type: application/force-download");  
		header("Content-Type: application/octet-stream");  
		header("Content-Type: application/download");  
		header('Content-Disposition:inline;filename="'.$output.'"');  
		header("Content-Transfer-Encoding: binary");  
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");  
		header("Pragma: no-cache");  
		$objWriter->save('php://output'); 
		*/ 		
	}

    /**
     * 导出文件excel，行数据与表头标题相对应，暂未添加单元格样式和对齐
     * @param $fileName 文件名称
     * @param $headArr 表头标题
     * @param $countArr 总数标题
     * @param $data  行数据
     * $param $count_data 最后一行总统计数据
     */
    function export_excel($fileName,$headArr,$countArr,$data,$count_data){

        require_once SYS_ROOT.'PHPExcel/PHPExcel.php';
        require_once SYS_ROOT."PHPExcel/PHPExcel/Writer/IWriter.php";
        require_once SYS_ROOT.'PHPExcel/PHPExcel/Writer/Excel2007.php';
        require_once SYS_ROOT.'PHPExcel/PHPExcel/Writer/Excel5.php';
        include_once SYS_ROOT.'PHPExcel/PHPExcel/IOFactory.php';

        if(empty($fileName)){
            exit;
        }
        $fileName .= ".xlsx";
        //创建新的PHPExcel对象
        $objPHPExcel = new PHPExcel();
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        //设置表头
        $key = ord("A");
        foreach($headArr as $v){
            $colum = chr($key);
            $objPHPExcel->getActiveSheet()->setCellValue($colum.'1', $v);
            $key += 1;
        }

        //循环写入数据
        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        if(!empty($data)){
            foreach($data as $rows){ //行写入
                $span = ord("A");
                foreach($headArr as $k=>$value){// 列写入
                    $j = chr($span);
                    if(in_array($k,array_keys($rows))){
                        $objActSheet->setCellValue($j.$column, $rows[$k]);
                    }
                    $span++;
                }
                $column++;
            }
        }
        //总计
        $_active_sheet = $objPHPExcel->getSheet(0);
        $_all_rows  = $_active_sheet->getHighestRow(); // 取得总行数
        if(!empty($count_data)){
            $span = ord("A");
            foreach($countArr as $k=>$value){// 列写入
                $j = chr($span);
                $objActSheet->setCellValue($j.($_all_rows+1), '总计');
                if(in_array($k,array_keys($count_data))){
                    $objActSheet->setCellValue($j.($_all_rows+1), $count_data[$k]);
                }
                $span++;
            }
        }

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //重命名表
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);

        //写入到文件
        $outputFileName = APPLICATION.'/caches/data/'.$fileName;
        $objWriter->save($outputFileName);
        //下载
        load_sys_func('extend');
        file_down($outputFileName,$fileName,1);
    }
    
    /**
     * 读取excel $filename 路径文件名 $encode 返回数据的编码 默认为utf8
     *以下基本都不要修改
     */
    public function read_excel($filename='',$extension,$encode='utf-8'){
    	
				require_once SYS_ROOT.'PHPExcel/PHPExcel.php';
				require_once SYS_ROOT."PHPExcel/PHPExcel/Writer/IWriter.php";
				require_once SYS_ROOT.'PHPExcel/PHPExcel/Writer/Excel2007.php';
				require_once SYS_ROOT.'PHPExcel/PHPExcel/Writer/Excel5.php';
				include_once SYS_ROOT.'PHPExcel/PHPExcel/IOFactory.php';
				if( $extension =='xlsx' )
				{
					$objReader = new PHPExcel_Reader_Excel2007();
				}
				else
				{
					$objReader = new PHPExcel_Reader_Excel5();
				}
				$objPHPExcel = $objReader->load($filename);
				$sheet = $objPHPExcel->getSheet(0);
				$highestRow = $sheet->getHighestRow(); // 取得总行数
				$highestColumn = $sheet->getHighestColumn(); // 取得总列数
				$hcount = PHPExcel_Cell::columnIndexFromString($highestColumn);
				$str = '';
				$excelData = array();
				//循环读取excel文件,读取一条,插入一条
				for($j=2;$j<=$highestRow;$j++)
				{
					
					for($aa=0;$aa<=$hcount;$aa++)
					{
					$zm = PHPExcel_Cell::stringFromColumnIndex($aa);
						
						$str .= iconv('utf-8','utf-8',$objPHPExcel->getActiveSheet()->getCell("$zm$j")->getValue()).'\\';//读取单元格
					}
				//explode:函数把字符串分割为数组。
				$strs = explode("\\",$str);
				$excelData[]=$strs;
				$str = "";
				}
				
				unlink($filename); //删除上传的excel文件
				return $excelData;
        }

}