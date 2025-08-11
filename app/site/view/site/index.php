<?php view_header(lang('网站首页'));?>
    

 
    <!-- 导航栏 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">网站LOGO</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">首页</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">产品</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">服务</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">关于我们</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- 主横幅 -->
    <header class="bg-light py-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">欢迎来到我们的网站</h1>
            <p class="lead">简洁高效的解决方案</p>
            <a href="#" class="btn btn-primary btn-lg mt-3">立即开始</a>
        </div>
    </header>

    <!-- 内容区 -->
    <main class="container my-5">
        <div class="row g-4">
            <!-- 卡片1 -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">特色功能</h5>
                        <p class="card-text">提供最先进的技术支持，满足您的业务需求。</p>
                    </div>
                </div>
            </div>
            
            <!-- 卡片2 -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">专业团队</h5>
                        <p class="card-text">经验丰富的团队为您提供全方位服务。</p>
                    </div>
                </div>
            </div>
            
            <!-- 卡片3 -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">客户案例</h5>
                        <p class="card-text">查看我们成功的客户合作案例。</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- 页脚 -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>联系我们</h5>
                    <p>邮箱: contact@example.com</p>
                </div>
                <div class="col-md-6">
                    <h5>关注我们</h5>
                    <p>
                        <a href="#" class="text-white me-3">Facebook</a>
                        <a href="#" class="text-white me-3">Twitter</a>
                        <a href="#" class="text-white">LinkedIn</a>
                    </p>
                </div>
            </div>
            <div>
                <?= \modules\admin\lib\Beian::output();?>
            </div>
        </div>
    </footer>

<?php view_footer();?>
