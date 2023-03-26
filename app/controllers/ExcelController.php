<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductPrices;
use App\System\Controller;

class Excel extends Controller
{
    public function exportExcel($id = null)
    {
        $obj = $_GET["obj"];
        // for ile diğerlerini 2. satırdan başlayarak dön Asatırsayısı


        switch ($obj) {
            case 'product':
                $this->exportPruductList();
                break;

            default:
                # code...
                break;
        }
    }

    public function exportPruductList()
    {
        $excel = new \PHPExcel();
        $excel->getActiveSheet()->setTitle("Ürün Listesi");
        $alphabetArray = range('A', 'Z');

        $productModel = new Product();
        $products = $productModel->getList(1, 50000);
        $productHeaders = array_keys(get_object_vars($products[0]));

        $imageModel = new Image();

        $incr = 0;
        foreach ($productHeaders as $item) {
            $excel->getActiveSheet()->setCellValueByColumnAndRow($incr, 1, ucfirst($item));
            // $excel->getActiveSheet()->setCellValue($alphabetArray[$incr] . "1", ucfirst($item));
            $excel->getActiveSheet()->getColumnDimension($alphabetArray[$incr])->setAutoSize(false);
            $incr++;
        }
        for ($i = 0; $i < 20; $i++) {
            $excel->getActiveSheet()->setCellValueByColumnAndRow($incr, 1, "Resimlink" . ($i + 1));
            $incr++;
        }

        $incr = 0;

        for ($i = 1; $i < count($products) + 1; $i++) {
            foreach ($productHeaders as $item) {
                $excel->getActiveSheet()->setCellValueByColumnAndRow($incr, $i + 1, $products[$i - 1]->{$item});
                $incr++;
            }
            $images = $imageModel->getFromElementId(1, $products[$i - 1]->id);
            foreach ($images as $img) {
                $excel->getActiveSheet()->setCellValueByColumnAndRow($incr, $i + 1, SITEADRESS . "assets/images/products/" . $img["path"]);
                $incr++;
            }
            $incr = 0;
        }




        $excel->getActiveSheet()->getStyleByColumnAndRow(0, 1, count($productHeaders) + 19, 1)
            ->getFill()
            ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setRGB("E5AB9E");
        $excel->getActiveSheet()->getStyleByColumnAndRow(0, 1, count($productHeaders) + 19, 1)->getFont()->setBold(true);

        

        ob_end_clean();
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Urun-Listesi.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0



        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('php://output');
    }

    public function getExcel($path)
    {
        // $path = "C:\Users\serif\Downloads\Urun-Listesi (1).xlsx";
        $excel_obj = \PHPExcel_IOFactory::load($path);

        //$worksheet = $excel_obj->->getActiveSheet();
        $worksheet = $excel_obj->getSheet('0');

        // $fistcolval = $worksheet->getCell('A1')->getValue();
        // $lastRow = $worksheet->getHighestRow();
        // $lastColString = $worksheet->getHighestDataColumn();
        // $lastColumnIndex = \PHPExcel_Cell::columnIndexFromString($lastColString);

        // for ile row da ve içinde colda dön bir diziye at
        //  $worksheet->getCell(\PHPExcel_Cell::stringFromColumnIndex(2))->getValue();

        // $excelValues = $objPHPExcel->setActiveSheetIndex(0)->rangeToArray()

        // Garbage collect...
        // $worksheet->garbageCollect();

        //    Identify the range that we need to extract from the worksheet
        $maxCol = $worksheet->getHighestColumn();
        $maxRow = $worksheet->getHighestRow();


        return $worksheet->rangeToArray("A1:" . $maxCol . $maxRow);
        // return $worksheet->toArray(null, true, true, false);
    }

    public function importProductListExcel()
    {
        try {
            if (move_uploaded_file($_FILES["excelfile"]["tmp_name"], "assets/importexcels/" . $_FILES["excelfile"]["name"])) {
                $data = $this->getExcel("assets/importexcels/" . $_FILES["excelfile"]["name"]);

                $categories = (new Category())->get();


                //başlıkları küçült
                for ($i=0; $i < count($data[0]); $i++) { 
                    $data[0][$i]= strtolower($data[0][$i]);
                }


                for ($i = 1; $i < count($data); $i++) {
                    $product = new Product();
                    $product->code = $data[$i][array_search('code', $data[0])];
                    $product->name = $data[$i][array_search('name', $data[0])];
                    $product->content = $data[$i][array_search('content', $data[0])];
                    $product->seourl = $data[$i][array_search('seourl', $data[0])];
                    for ($j = 0; $j < count($categories); $j++) {
                        $aa = array_search('DAVETİYE', $categories[$j]);
                        if ($aa) {
                            $product->categoryid = $categories[$j]["id"];
                        }
                    }
                    $product->insert();
                    
                    $priceModel = new ProductPrices();
                    $priceModel->prices = floatval($data[$i][array_search('prices', $data[0])]);
                    $priceModel->taxtype = intval($data[$i][array_search('taxtype', $data[0])]);
                    $priceModel->productid = $product->id;
                    $priceModel->addid = 1;
                    $priceModel->insert();
                    $product->priceid = $priceModel->id;

                    

                    for ($z = 1; $z < 20; $z++) {
                        $path = $data[$i][array_search('resimlink' . $z, $data[0])];
                        if ($path!="") {
                            $image = new Image();
                            $image->addid = 1;
                            $image->guuid = uniqid();
                            $image->dimensions = 1;
                            $image->visibleorder = $z;
                            $image->title = "";
                            $image->addid = 1;
                            $image->elementtypeno = 1;
                            $image->elementtypeid = $product->id;
                            $image->saveImageFromUrl($path,"assets/images/products/");
                        }
                    }
                }
            } else {
                echo "error : " . $_FILES["file"]["error"];
            }
        } catch (\Throwable $th) {
            echo "error : ";
        }
    }
}
