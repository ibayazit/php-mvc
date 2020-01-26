<?php

class Console
{
    public function execute($argv){
        switch ($argv[0]) {
            case 'make:model':
                return $this->create('model', $argv[1]);
                break;
            case 'make:controller':
                return $this->create('controller', $argv[1]);
                break;
            case 'make:middleware':
                return $this->create('middleware', $argv[1]);
                break;
            
            default:
                return 'Undefined command.';
                break;
        }
    }

    private function create($type, $fileName){
        if(!$fileName)
            return 'Error! There is no ' . $type . ' name.';
        // Alınan dosya adı parçalanır ve dosya adı hariç alınır
        $preparedFilePath = explode('/', $fileName, -1);
        // Dosya yolu namespace için kullanılacak
        $Path = implode('\\', $preparedFilePath);
        // Dosya adı
        $Name = end(explode('/', $fileName));
        // Dosya oluşturma ve dizin oluşturma için kullanılacak
        $tempPath = $GLOBALS['PATH'] . '/app/' . $type;

        // Dizinler oluşturulur
        foreach($preparedFilePath as $pFilePath){
            $tempPath = $tempPath . '/' . $pFilePath;
            if(!file_exists($tempPath))
                mkdir($tempPath);
        }

        // işleme ait kaynak dosyası
        $source_path = $GLOBALS['PATH'] . '/app/provider/console/source_file/' . $type . '.php';
        // dosyanın kaydedileceği yol
        $target_path = $tempPath . '/' . $Name . '.php';
        
        // Kaynak dosyası okunur ve hazırlanır
        $content = file_get_contents($source_path);

        $content = "<?php\n" . $content;

        $content = str_replace('{namespace}', ($Path ? '\\' . $Path : ''), $content);
        $content = str_replace('{class}', $Name, $content);

        if($type === 'model'){
            $content = str_replace('{table}', strtolower($Name), $content);
        }

        // Dosya yok ise oluşturulur aksi halde uyarı döndürülür.
        if(!file_exists($target_path)){
            file_put_contents($target_path, $content);
            return 'File created.';
        }
        else
            return 'File already exist!';
    }
}