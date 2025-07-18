import { defineConfig } from 'vitepress'

// https://vitepress.vuejs.org/config/app-configs
export default defineConfig({
    lang: 'zh-CN',
    title: 'NexoPHP',
    description: 'NexoPHP 是一个基于 PHP 8.x 版本的框架，它的目标是为 PHP 开发者提供一个简单、快速、安全的软件开发框架',

    /**
     * 菜单 
     */
    themeConfig: {
        nav: [
            { text: '首页', link: '/' },
            { text: '指南', link: '/guide' },
            { text: '数据库', link: '/db' }, 
            { text: '函数', link: '/fun' }, 
            { text: 'Action', link: '/action' }, 
            { text: '模块', link: '/module' }, 
        ],

        // 添加搜索功能
        search: {
            provider: 'local',
            options: {
                locales: {
                    root: {
                        translations: {
                            button: {
                                buttonText: '搜索文档',
                                buttonAriaLabel: '搜索文档'
                            },
                            modal: {
                                noResultsText: '无法找到相关结果',
                                resetButtonTitle: '清除查询条件',
                                footer: {
                                    selectText: '选择',
                                    navigateText: '切换',
                                    closeText: '关闭'
                                }
                            }
                        }
                    }
                }
            }
        }
    },
})