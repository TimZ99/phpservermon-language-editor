<?php
/**
 * PHP Server Monitor Language Editor
 * Helps maintaining the translations files of PHP Server Monitor.
 *
 * This file is part of PHP Server Monitor Language Editor (PSMLE).
 * PHP Server Monitor Language Editor is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License as 
 * published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * PHP Server Monitor Language Editor is distributed in the hope
 * that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHP Server Monitor.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     phpservermon-language
 * @author      Tim Zandbergen <Tim@Xervion.nl>
 * @copyright   Copyright (c) 2018 Tim Zandbergen <Tim@Xervion.nl>
 * @license     http://www.gnu.org/licenses/gpl.txt GNU GPL v3
 * @version     v0.2
 * @link        http://www.github.com/TimZ99/phpservermon-language/
 * 
 * @todo save changes
 * @todo finish documentation
 **/

//debug
ini_set('display_errors', 1);
error_reporting(E_USER_ERROR);

//settings
$path = 'lang';
$translationLang = 'nl_NL.lang.php';

//get files in folder
$files = scandir($path);

//check if path is directory
if(!$files){
    trigger_error("Path $path is not a directory.", E_USER_ERROR);
}
//check if default lang exists
if(!in_array('en_US.lang.php', $files)){
    trigger_error("Default lang en_US not found.", E_USER_ERROR);
}
//check if translation file exists
if(!in_array($translationLang, $files)){
    trigger_error("$translationLang not found.", E_USER_ERROR);
}
//check if default lang file is readable
if(!is_readable($path.'/en_US.lang.php')){
    trigger_error("Default lang file not readable.", E_USER_ERROR);
}
//check if translation file is readable
if(!is_readable($path."/".$translationLang)){
    trigger_error("$translationLang not readable.", E_USER_ERROR);
}

//get content of default lang
include($path.'/en_US.lang.php');
$default = $sm_lang;
unset($sm_lang);

//get content of translated lang
include($path.'/'.$translationLang);
$translation = $sm_lang;
unset($sm_lang);

/**
 * Display the default language and translation.
 * @param array $default Array containing every default translation.
 * @param array $translation Array containing every translation for the translation file.
 * @param string $prevKey Default ''. Otherwise previous key.
 * @param int $px Default 0. Left margin for input fields, used for indentation.
 */ 
function displayHTML(array $default, array $translation, string $prevKey = '', int $px = 0){
    /**
     * Changes border of input to red if no translation is found.
     * @var string
     */
    $empty = 'border: 1px red solid;';

    /**
     * Changes border of input to orange if translation is the same as default.
     * @var string
     */
    $same = 'border: 1px orange solid;';

    /**
     * Final value before echo. '' if no translation was found.
     * @var string
     */
    $trans = '';

    /**
     * Style of inputfield.
     * @var string
     */
    $style = '';

    foreach($default as $key => $value){
        if(is_array($value)){
            echo "<input style=\"margin:5px 12px 0px ".$px."px;\" name=\"$key\" type=\"text\" value=\"".htmlspecialchars($key)."\"><br>\n\t";
            displayHTML($value, $translation[$key], $key, 24);
        }
        else{
            $trans = '';
            $style = $empty;
            /**
             * Check if key exists in translation.
             * No -> border red.
             */
            if(array_key_exists($key, $translation)){
                $trans = $translation[$key];
                $style = "";

                if($trans == $value){
                    $style = $same;
                }
            }
            echo "<input style=\"margin:5px 12px 0px ".$px."px;\" type=\"text\" value=\"".htmlspecialchars($key)."\">\n\t";
            echo "<input style=\"margin:5px 12px 0px ".$px."px;\" type=\"text\" value=\"".htmlspecialchars($value)."\">\n\t";

            if($prevKey != ''){
                $key = $prevKey.".".$key;
            }

            echo "<input style=\"$style\" type=\"text\" name=\"$key\" value=\"".htmlspecialchars($trans)."\"><br>\n\t";
        }
    }
}

/**
 * Save translation
 * @todo add option to show output using textarea
 * @todo create new content
 * @return string
 */ 
function saveTranslation(){

    return modifyFile($content);
}

/**
 * Load changes to translation file.
 * Copy documentation and past the new translation under it.
 * @param string $newContent New content for file. Default set to prevent errors.
 * @return string
 */
function modifyFile($newContent = ''){
    global $path;
    global $translationLang;
    $currentContent = file_get_contents($path."/".$translationLang);
    $licenseAndDocs = substr($currentContent,0,strpos($currentContent, '$sm_lang'));
    file_put_contents($path.$translationLang, $licenseAndDocs."\n".$newContent);
    return "Success";
}

/**
 * Check if file is writable. (Currently required to save.)
 * Check if $_POST["submit"] isset.
 * @return string
 */
function checkForSave(){
    global $path;
    global $translationLang;
    $message = false;
    $messageBox = "<div style=\"width:50vw; margin-left:25vw; padding:12px; border:black 1px solid; border-radius:5px;\">";

    if (!is_writable($path.$translationLang)) {
        $message = true;
        $messageBox .= "<b>File not writable.</b><br>Permission: ".substr(sprintf('%o', fileperms($path."/test.php")), -4).".<br>Should be 0666.";
    }
    if(isset($_POST["submit"])){
        $message = true;
        $messageBox .= saveTranslation();
    }

    $messageBox .= "</div><br><br>";
    return $message ? $messageBox : '';
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>
        PSMLE - <?php echo $translationLang; ?>
        </title>
        <style>
            input{width:30vw;} 
            ul{list-style-type: circle;}
            button{
                width: 50vw; height: 50px;
                border-radius: 10px;
                background-color: rgb(75, 205, 20);
                border: black 1px solid;
                color: white; font-size: 15px;
                margin: 24px 25vw;
            }
            button:active{background-color: rgb(43, 117, 12);}
            button:hover{background-color: rgb(61, 166, 16);}

        </style>
        <!--[if lt IE 9]>
            <script src="/js/html5shiv.js"></script>
        <![endif]-->
    </head>
    <body>
        <ul>
            <li>Red: no translation found.</li>
            <li>Orange: possibly not translated.</li>
        </ul>
        <br>
        <b>
            DON'T FORGET TO SAVE!
            <br><br>
            If you want to change the default translation, select it as the translation file.
        </b>
        <br><br>
        <?php
        echo checkForSave();
        ?>
        <input style="margin:5px 12px 0px 0px;" value="Key">
        <input style="margin:5px 12px 0px 0px;" value="Default">
        <input style="margin:5px 12px 0px 0px;" value="Translation">
        <br><br><br>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <?php 
        displayHTML($default, $translation); 
        ?>
        <button type="submit" name="submit" value="1">Save translation</button>
        </form>
    </body>
</html>