<?php

namespace modules\admin\data;

class UploadData
{
    /**
     * 文件类型
     */
    public static $mime_group = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp'],
        'video' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'],
        'audio' => ['mp3', 'wav', 'aac', 'flac', 'ogg', 'm4a'],
        'file' => ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
        'pdf' => ['pdf'],
    ];
    /**
     * 文件类型名称
     */
    public static $mime_group_name = [
        'image' => '图片',
        'video' => '视频',
        'audio' => '音频',
        'file' => '文件',
        'pdf' => 'PDF',
    ];
    /**
     * 文件类型图标
     */
    public static  $mime_group_icon = [
        'image' => '/misc/img/image.png',
        'video' => '/misc/img/video.png',
        'audio' => '/misc/img/audio.png',
        'doc' => '/misc/img/doc.png',
        'docx' => '/misc/img/doc.png',
        'xls' => '/misc/img/xls.png',
        'xlsx' => '/misc/img/xls.png',
        'ppt' => '/misc/img/ppt.png',
        'pptx' => '/misc/img/ppt.png',
        'pdf' => '/misc/img/pdf.png',
    ];
    /**
     * 获取允许上传的文件后缀
     */
    public static function getAllowExt($type = '')
    {
        $exts = [];
        if ($type) {
            $exts = self::$mime_group[$type];
        } else {
            foreach (self::$mime_group as $group => $ext) {
                $exts = array_merge($exts, $ext);
            }
        }
        return $exts;
    }
    /**
     * upload每行记录添加group字段，区分类型
     */
    public static function get(&$data)
    {
        if (!$data || !is_array($data)) {
            return;
        }
        $ext = $data['ext'];
        $data['group'] = '';
        $data['group_name'] = '';
        $data['group_icon'] = '';
        foreach (self::$mime_group as $group => $exts) {
            if (in_array($ext, $exts)) {
                $data['group'] = $group;
                $data['group_name'] = lang(self::$mime_group_name[$group]);
                $data['group_icon'] = self::$mime_group_icon[$ext]?:self::$mime_group_icon[$group];
                break;
            }
        } 
    }
}
