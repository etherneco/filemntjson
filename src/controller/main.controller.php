<?php

class MainController extends Controller {

    private $data = [];

    public function index() {
        $this->checkLogin();
        $this->checkXSRF();
        $this->checkDir();

        $method = 'do'.ucfirst($_REQUEST['do']).'Action';
        //die($method);
        if(method_exists($this, $method))
            $this->$method();

        $this->data['MAX_UPLOAD_SIZE'] = min(Lib::countBytes(ini_get('post_max_size')), Lib::countBytes(ini_get('upload_max_filesize')));
        echo MainController::displayViews($this->data, 'main');
    }

    private function checkLogin() {
        if (ACCESS_PASSWORD) {
            session_start();
            if (!$_SESSION['_ether_allowed']) {
                // sha1, and random bytes to thwart timing attacks.  Not meant as secure hashing.
                $t = bin2hex(openssl_random_pseudo_bytes(10));
                if ($_POST['p'] && sha1($t . $_POST['p']) === sha1($t . ACCESS_PASSWORD)) {
                    $_SESSION['_ether_allowed'] = true;
                    header('Location: ?');
                }
                echo '';
                exit;
            }
        }
    }

    private function checkXSRF() {
        if (!$_COOKIE['_ether_xsrf'])
            setcookie('_ether_xsrf', bin2hex(openssl_random_pseudo_bytes(16)));
        if ($_POST) {
            if ($_COOKIE['_ether_xsrf'] !== $_POST['xsrf'] || !$_POST['xsrf'])
                $this->errorHTTP(403, "XSRF Failure");
        }
    }

    private function checkDir() {
        $tmp_dir = dirname($_SERVER['SCRIPT_FILENAME']);
        if (DIRECTORY_SEPARATOR === '\\')
            $tmp_dir = str_replace('/', DIRECTORY_SEPARATOR, $tmp_dir);
        $tmp = Lib::get_absolute_path($tmp_dir . '/' . $_REQUEST['file']);

        if ($tmp === false)
            $this->errorHTTP(404, 'File or Directory Not Found');
        if (substr($tmp, 0, strlen($tmp_dir)) !== $tmp_dir)
            $this->errorHTTP(403, "Forbidden");
        if (strpos($_REQUEST['file'], DIRECTORY_SEPARATOR) === 0)
            $this->errorHTTP(403, "Forbidden");
    }

    private function doListAction() {
        $file = $_REQUEST['file'] ?: '.';
        if (is_dir($file)) {
            $directory = $file;
            $result = [];
            $files = array_diff(scandir($directory), ['.', '..']);
            foreach ($files as $entry)
                if (!Lib::is_entry_ignored($entry, set_show_folders, Config::$hidden_extensions)) {
                    $i = $directory . '/' . $entry;
                    $stat = stat($i);
                    $result[] = [
                        'name' => basename($i),
                        'mtime' => $stat['mtime'],
                        'size' => $stat['size'],
                        'path' => preg_replace('@^\./@', '', $i),
                        'is_dir' => is_dir($i),
                        'is_deleteable' => set_delete && ((!is_dir($i) && is_writable($directory)) || (is_dir($i) && is_writable($directory) && Lib::is_recursion_delete($i))),
                        'is_readable' => is_readable($i),
                        'is_writable' => is_writable($i),
                        'is_executable' => is_executable($i),
                    ];
                }
        } else {
            $this->errorHTTP(412, "Not a Directory");
        }

        echo json_encode(['success' => true, 'is_writable' => is_writable($file), 'results' => $result]);
        exit;
    }

    private function doUploadAction() {
        if(!set_upload) exit;
        $file = $_REQUEST['file'] ?: '.';
        foreach (Config::$disallowed_extensions as $ext)
            if (preg_match(sprintf('/\.%s$/', preg_quote($ext)), $_FILES['file_data']['name']))
                $this->errorHTTP(403, "Files of this type are not allowed.");
        $res = move_uploaded_file($_FILES['file_data']['tmp_name'], $file . '/' . $_FILES['file_data']['name']);
        exit;
    }

    private function doDownloadAction() {
        
        $file = $_REQUEST['file'] ?: '.';
        $filename = basename($file);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        header('Content-Type: ' . finfo_file($finfo, $file));
        header('Content-Length: ' . filesize($file));
        header(sprintf('Content-Disposition: attachment; filename=%s',
                        strpos('MSIE', $_SERVER['HTTP_REFERER']) ? rawurlencode($filename) : "\"$filename\"" ));
        ob_flush();
        readfile($file);
        exit;
    }

    private function doMkdirAction() {
        if(!set_create_folder) exit;
        $file = $_REQUEST['file'] ?: '.';
        $dir = $_POST['name'];
        $dir = str_replace('/', '', $dir);
        if (substr($dir, 0, 2) === '..')
            exit;
        chdir($file);
        @mkdir($_POST['name']);
        exit;
    }

    private function doDeleteAction() {
        if(!set_delete)exit;
        $file = $_REQUEST['file'] ?: '.';
        Lib::rmrf($file);
        exit;
    }

}
