<?php
namespace CuratorC\Coder\Traits;

use Log;

trait FileOperation
{

    /**
     * 删除文件夹
     * @param            [type] $dir [description]
     * @return           [type]      [description]
     * @author ' . config('coder.file_title.author_name') . '
     * @date(2020-01-27)
     */
    public function deldir($dir)
    {
        //先删除目录下的文件：
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullpath=$dir."/".$file;
                if(!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    deldir($fullpath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 代码写入文件
     * @param            [type] $filePath    [description]
     * @param            [type] $fileContent [description]
     * @return           [type]              [description]
     * @author ' . config('coder.file_title.author_name') . '
     * @date(2020-01-29)
     */
    public function writeToFile($filePath, $fileContent)
    {
        // 写入文件
        if(file_exists($filePath)) {
            $this->addMessage($filePath . ' 文件已存在！', 'red');
        }
        $file = fopen($filePath, 'a+');
        fputs($file, $fileContent);
        fclose($file);
        $this->addMessage($filePath . ' 文件写入！', 'green');
    }


    /**
     * 注册文件
     * @param            [type] $filePath    [description]
     * @param            [type] $fileContent [description]
     * @param            [type] $mark        [description]
     * @return           [type]              [description]
     * @author ' . config('coder.file_title.author_name') . '
     * @date(2020-01-29)
     */
    public function registerFile($filePath, $registerContent, $mark)
    {
        $fileContent = file_get_contents($filePath);
        $fileArray = explode($mark, $fileContent);
        // 正确分割判断
        if (count($fileArray) != 2) {
            $this->addMessage($filePath . '文件注册失败！', 'red');
            return false;
        }
        if ($fileArray[0]) $text = $fileArray[0] . $registerContent . $mark . $fileArray[1];
        $file = fopen($filePath, 'w+');
        fwrite($file, $text);
        fclose($file);
        $this->addMessage($filePath . ' 文件注册！', 'green');
        return true;
}

    /**
     * 初始化检测
     * @return           bool 初始化状态， true 为需要解压文件， false 为无需解压文件
     * @author ' . config('coder.file_title.author_name') . '
     * @date(2020-01-26)
     */
    public function initProject()
    {
        // 判断初始文件是否存在。若存在，删除之。
        $configModelPath = config_path() . '/coder.php';
        $migrationsPath = [
            app_path() . '/../database/migrations/2014_10_12_000000_create_users_table.php',
            app_path() . '/../database/migrations/2014_10_12_100000_create_password_resets_table.php',
        ];

        if (!file_exists($configModelPath)) {
            foreach ($migrationsPath as $item) {
                if (file_exists($item)) unlink($item);
            }

            // 发布文件
            $this->publishesBasicFile();
            return true;
        } else {
            return false;
        }
    }

    private function publishesBasicFile()
    {
        $this->copyDir(app_path() . '/../vendor/curatorc/coder/publishes/', app_path() . '/../');
    }

    /**
     * 文件夹文件拷贝
     *
     * @param string $src 来源文件夹
     * @param string $dst 目的地文件夹
     * @return bool
     */
    private function copyDir($src = '', $dst = '')
    {
        if (empty($src) || empty($dst))
        {
            return false;
        }

        $dir = opendir($src);
        $this->createDir($dst);

        while (false !== ($file = readdir($dir)))
        {
            if (($file != '.') && ($file != '..'))
            {
                if (is_dir($src . '/' . $file))
                {
                    $this->copyDir($src . '/' . $file, $dst . '/' . $file);
                }
                else
                {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);

        return true;
    }

    /**
     *ㅤ创建文件夹
     * @param string $path 文件夹路径
     * @param int $mode 访问权限
     * @param bool $recursive 是否递归创建
     * @return bool
     * @date 2020/11/17
     * @author ' . config('coder.file_title.author_name') . '
     */
    public function createDir($path = '', $mode = 0777, $recursive = true)
    {
        clearstatcache();
        if (!is_dir($path))
        {
            mkdir($path, $mode, $recursive);
            return chmod($path, $mode);
        }

        return true;
    }




    // 驼峰转下划线
    public function createUnderScore($string)
    {
        return lcfirst(strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $string)));
    }

    // 下划线转驼峰
    public function createBigHump($uncamelized_words, $separator = '_')
    {
        $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
        return ucfirst(ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator));
    }

    /**
     * name 单词单数转成复数
     * @param $string
     * @return mixed
     * by '.$this->articleName.'
     * at 2019/6/19 9:27
     */

    public function pluralize($string)
    {
        $string = strtolower($string);
        //plural pattern
        $plural = array(
            array('/(quiz)$/i', "$1zes"),
            array('/^(ox)$/i', "$1en"),
            array('/([m|l])ouse$/i', "$1ice"),
            array('/(matr|vert|ind)ix|ex$/i', "$1ices"),
            array('/(x|ch|ss|sh)$/i', "$1es"),
            array('/([^aeiouy]|qu)y$/i', "$1ies"),
            array('/([^aeiouy]|qu)ies$/i', "$1y"),
            array('/(hive)$/i', "$1s"),
            array('/(?:([^f])fe|([lr])f)$/i', "$1$2ves"),
            array('/sis$/i', "ses"),
            array('/([ti])um$/i', "$1a"),
            array('/(buffal|tomat)o$/i', "$1oes"),
            array('/(bu)s$/i', "$1ses"),
            array('/(alias|status)$/i', "$1es"),
            array('/(octop|vir)us$/i', "$1i"),
            array('/(ax|test)is$/i', "$1es"),
            array('/s$/i', "s"),
            array('/$/', "s")
        );


        //irregular 不规则
        $irregular = array(
            array('move', 'moves'),
            array('sex', 'sexes'),
            array('child', 'children'),
            array('man', 'men'),
            array('person', 'people')
        );
        //uncountable 不可数
        $uncountable = array(
            'sheep',
            'fish',
            'series',
            'species',
            'money',
            'rice',
            'information',
            'equipment'
        );

        if (in_array($string, $uncountable)) {
            return $string;
        }

        foreach ($irregular as $noun) {
            if ($string == $noun[0]) {
                return $noun[1];
            }
        }

        foreach ($plural as $pattern) {
            $str = preg_replace($pattern[0], $pattern[1], $string);
            if ($str !== null && $str != $string) {
                return $str;
            }
        }
    }
}