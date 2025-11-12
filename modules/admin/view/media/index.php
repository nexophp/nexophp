<?php
add_css("/assets/admin/media.css");
?>
<?php
view_header(lang('媒体库'));
global $vue;
$url = '/admin/media/ajax?type=' . $type;

// 基础数据初始化
$vue->data("height", "");
$vue->data("viewMode", "grid"); // grid 或 list
$vue->data("selectedImage", "");
$vue->data("previewVisible", false);
$vue->data("selectedItems", []); // 选中的图片数组

$vue->created(["load()"]);

// 初始化高度
$vue->method("load()", "
this.height = 'calc(100vh - " . get_config('admin_table_height') . "px)';
");

// 切换视图模式
$vue->method("toggleViewMode()", "
this.viewMode = this.viewMode === 'grid' ? 'list' : 'grid';
");

// 预览图片
$vue->method("previewImage(item, event)", "
event.stopPropagation();
this.selectedImage = item.url;
this.previewVisible = true;
");


// 处理文件点击事件
$vue->method("handleFileClick(item)", "
if (item.group === 'word') {
    this.open_office(item.url);
} else if (item.group === 'pdf') {
    this.open_pdf(item.url);
} else if (item.group === 'image') {
    this.previewImage(item, event);
} else {
    // 其他文件类型直接下载或打开
    window.open(item.url, '_blank');
}
");

// 切换选中状态
$vue->method("toggleSelection(item)", "
const index = this.selectedItems.findIndex(selected => selected.id === item.id);
if (index > -1) {
    // 如果已选中，则取消选中
    this.selectedItems.splice(index, 1);
} else {
    // 如果未选中，则添加到选中列表
    this.selectedItems.push(item);
}
");

// 检查是否选中
$vue->method("isSelected(item)", "
return this.selectedItems.some(selected => selected.id === item.id);
");

// 清空选中
$vue->method("clearSelection()", "
this.selectedItems = [];
");


$vue->data("show_btn", false);
$vue->method("do_select()", "");
if ($js) {
    $js = "const parentVue = window.parent.app; " . $js;
    $vue->method("click_image(data)", $js);
} else if ($mjs) {
    $vue->method("click_image(data)", "
        this.select.push(data); 
    ");
    $vue->data("show_btn", true);
    $mjs = "const parentVue = window.parent.app; 
        let data = this.selectedItems;
        console.log(data);
    " . $mjs;
    $vue->method("do_select()", $mjs);
} else {
    $vue->method("click_image(data)", $mjs);
}

$vue->method("handleSuccess(res,file)", "
    if(res.code == 0) {
        this.load_list();
    }else{
        this.\$message.error(res.msg);
    }
");

// 在现有的 Vue 数据定义后添加（大约在第15行后）
$vue->data("contextMenuVisible", false);
$vue->data("contextMenuStyle", "");
$vue->data("contextMenuItem", null);

// 在现有的 Vue 方法定义后添加（大约在第70行后）
// 显示右键菜单
$vue->method("showContextMenu(item, event)", "
event.preventDefault();
event.stopPropagation();
this.contextMenuItem = item;
this.contextMenuStyle = {
    left: event.clientX + 'px',
    top: event.clientY + 'px'
};
this.contextMenuVisible = true;
");

// 隐藏右键菜单
$vue->method("hideContextMenu()", "
this.contextMenuVisible = false;
this.contextMenuItem = null;
");

// 删除文件
$vue->method("deleteFile(item)", "
this.\$confirm('".lang('确定要删除这个文件吗？')."', '".lang('提示')."', {
    confirmButtonText: '".lang('确定')."',
    cancelButtonText: '".lang('取消')."',
    type: 'warning'
}).then(() => {
    ajax('/admin/media/delete', {id: item.id},function(res){
        if (res.code == 0) {
            _this.\$message.success('".lang('删除成功')."');
            _this.load_list();
        } else {
            _this.\$message.error('".lang('删除失败')."');
        }
    });
});
this.hideContextMenu();
");

?>

<!-- 搜索过滤器 -->
<?php
echo element("filter", [
    'data' => 'list',
    'url' => $url,
    'is_page' => true,
    'init' => true,
]);
?>

<!-- 工具栏 -->
<div class="media-toolbar">
    <div>
        <?php
        echo element("pager", [
            'data' => 'list',
            'per_page' => get_config('per_page'),
            'per_page_name' => 'per_page',
            'url' => $url,
            'reload_data' => []
        ]);
        ?>
    </div>
    <div style="display: flex; align-items: center;">
        <!-- 选中计数器 -->
        <div v-if="selectedItems.length > 0" class="selected-counter">
            <?= lang('已选中') ?> {{selectedItems.length}} <?= lang('个文件') ?>
        </div>
        <el-button v-if="selectedItems.length > 0" type="danger" @click="clearSelection()">
            <?= lang('清空选中') ?>
        </el-button>
        <div v-if="show_btn && selectedItems.length >= 1" style="margin-left: 10px;">
            <el-button type="primary" @click="do_select()"><?= lang('确认所选') ?></el-button>
        </div>
    </div>
    <div class="view-toggle">
        <el-upload style="margin-right: 0px;"
            class="upload-hide"
            action="/admin/media/upload"
            multiple
            accept="<?= $accept ?>"
            :limit="100"
            :on-success="handleSuccess">
            <div class="view-toggle-btn" style="width: 100%;height:100%;background-color: red;border:0;color:#FFF;">
                <?= lang('上传新文件') ?>
            </div>
        </el-upload>
        <button
            class="view-toggle-btn"
            :class="{active: viewMode === 'grid'}"
            @click="viewMode = 'grid'">
            <i class="el-icon-menu"></i> <?= lang('网格') ?>
        </button>
        <button
            class="view-toggle-btn"
            :class="{active: viewMode === 'list'}"
            @click="viewMode = 'list'">
            <i class="el-icon-s-order"></i> <?= lang('列表') ?>
        </button>
    </div>
</div>

<!-- 网格视图 -->
<div v-if="viewMode === 'grid'" class="media-grid" @click="hideContextMenu()">
    <div v-for="item in list" :key="item.id"
        class="media-card"
        :class="{selected: isSelected(item)}"
        @contextmenu="showContextMenu(item, $event)"
        <?php if ($js): ?>
        @click="click_image(item)"
        <?php else: ?>
        @click="toggleSelection(item)"
        <?php endif; ?>>
        <div class="media-image-container">
            <!-- 根据文件类型显示不同的图标或图片 -->
            <img v-if="item.group === 'image'"
                :ref="'item_'+item.id"
                :src="item.url"
                :alt="item.filename"
                class="media-image"
                @click="previewImage(item, $event)"
                @error="$event.target.style.display='none'">

            <div v-else class="file-icon-container" @click="handleFileClick(item)">
                <img :src="item.group_icon" class="file-icon-1" :alt="item.filename">
                <div class="file-type-label">{{item.name}}</div>
            </div>

            <div v-if="item.group === 'image'" class="media-overlay" @click="previewImage(item, $event)">
                <i class="el-icon-zoom-in" style="color: #fff; font-size: 24px;"></i>
            </div>
            <div v-else class="media-overlay" @click="handleFileClick(item)">
                <i class="el-icon-document" style="color: #fff; font-size: 24px;"></i>
            </div>
        </div>

        <div class="media-info">
            <div class="media-filename" :title="item.filename">
                {{item.name}}
            </div>

            <div class="media-meta">
                <span class="media-size">{{(item.size)}}</span>
                <el-tag size="mini" :type="item.group === 'image' ? 'success' : item.group === 'pdf' ? 'danger' : item.group === 'word' ? 'primary' : 'info'">{{item.group_name}}</el-tag>
            </div>

            <div class="media-meta" v-if="item.width && item.height">
                <span class="media-dimensions">{{item.width}} × {{item.height}}</span>
                <span>{{item.created_at}}</span>
            </div>
            <div class="media-meta" v-else>
                <span>{{item.created_at}}</span>
            </div>
        </div>
    </div>
</div>

<!-- 列表视图 -->
<div v-if="viewMode === 'list'" class="media-list">
    <div v-for="item in list" :key="item.id"
        class="media-list-item"
        :class="{selected: isSelected(item)}"
        <?php if ($js): ?>
        @click="click_image(item)"
        <?php else: ?>
        @click="toggleSelection(item)"
        <?php endif; ?>>

        <!-- 根据文件类型显示不同的缩略图 -->
        <img v-if="item.group === 'image'"
            :src="item.url"
            :alt="item.filename"
            class="media-list-thumb"
            @click="previewImage(item, $event)"
            @error="$event.target.style.display='none'">

        <div v-else class="media-list-thumb file-thumb" @click="handleFileClick(item)">
            <img :src="item.group_icon" class="file-icon-small" :alt="item.filename">
        </div>

        <div class="media-list-info">
            <div class="media-list-filename">{{item.filename}}</div>
            <div class="media-list-meta">
                {{(item.size)}} •
                <span v-if="item.width && item.height">{{item.width}} × {{item.height}} • </span>
                <el-tag size="mini" :type="item.group === 'image' ? 'success' : item.group === 'pdf' ? 'danger' : item.group === 'word' ? 'primary' : 'info'">{{item.group}}</el-tag> • {{item.created_at}}
            </div>
        </div>
    </div>
</div>

<!-- 右键菜单 -->
<div v-show="contextMenuVisible"
    class="context-menu"
    :style="contextMenuStyle"
    @click.stop>
    <div class="context-menu-item" @click="deleteFile(contextMenuItem)">
        <i class="el-icon-delete"></i> <?= lang('删除') ?>
    </div>
</div>

<!-- 图片预览对话框 -->
<el-dialog
    :visible.sync="previewVisible"
    class="preview-dialog"
    width="90%"
    :show-close="true"
    :close-on-click-modal="true"
    :before-close="function() { previewVisible = false; }">
    <img :src="selectedImage" class="preview-image" alt="预览图片">
</el-dialog>

<?php
view_footer();
?>