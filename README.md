# ZeNote - 轻量级个人随手记工具

ZeNote 是一款基于 PHP + SQLite + Docker 构建的极简主义在线笔记应用，旨在提供快速、流畅的记录体验。它拥有仿苹果备忘录的黑白风格界面，支持深色模式、多语言切换以及强大的富文本编辑功能。

![ZeNote](https://via.placeholder.com/800x450?text=ZeNote+Preview)

## ✨ 核心特性

*   **极简设计**：采用 Tailwind CSS 打造，仿 Apple 备忘录风格，界面干净清爽。
*   **深色模式**：内置 Light/Dark 主题切换，完美适配系统设置。
*   **双语支持**：一键切换中文/英文界面。
*   **富文本编辑**：
    *   集成 Quill 编辑器，支持加粗、列表、代码块等。
    *   **图片支持**：直接粘贴或拖拽图片即可插入。
    *   **快捷工具**：右下角悬浮按钮提供“插入当前时间”和“JSON 格式化”功能。
*   **高效管理**：
    *   **全局搜索**：侧边栏实时搜索笔记标题和内容。
    *   **页内搜索**：支持 `Ctrl+F` 在当前笔记内高亮查找。
    *   **置顶功能**：重要笔记一键置顶。
    *   **无限滚动**：侧边栏支持分页懒加载，海量笔记也能流畅浏览。
*   **数据安全**：
    *   基于 SQLite，数据文件单一，备份迁移只需复制一个文件。
    *   支持用户注册/登录，数据按用户隔离。
*   **开箱即用**：提供 Docker Compose 配置，一键部署。

## 🚀 快速开始

### 1. 部署运行

确保你的机器上安装了 Docker 和 Docker Compose。

```bash
# 启动服务
docker compose up -d
```

服务启动后，访问 `http://localhost:9096` 即可使用。

### 2. 初始使用

1.  打开浏览器访问首页。
2.  点击底部的 **"创建新账号"** (Create an account) 进行注册。
3.  注册成功后自动跳转登录，即可开始你的记录之旅。

## 📂 项目结构

```text
znote/
├── docker-compose.yml   # 容器编排配置
├── Dockerfile           # 镜像构建文件
├── data/                # [自动生成] 数据库持久化目录
└── src/
    ├── config.php       # 数据库连接与表结构自动初始化
    └── public/          # Web 根目录
        ├── index.php    # 主界面 (SPA 单页应用体验)
        ├── login.php    # 登录/注册页
        └── api.php      # 后端 API 接口
```

## 🛠️ 技术栈

*   **后端**：PHP 8.2 (Apache)
*   **数据库**：SQLite3
*   **前端**：原生 JS + Tailwind CSS (CDN) + Quill.js
*   **部署**：Docker

## 📝 常用快捷键

*   **Ctrl + F / Cmd + F**：在当前笔记内容中搜索。
*   **Ctrl + S / Cmd + S**：手动触发保存（系统也会在你停止输入 1 秒后自动保存）。

## 🔧 开发调试

如果需要修改代码，直接编辑 `src/` 目录下的文件即可，修改后刷新浏览器立即生效（无需重启容器，因为 `src` 目录已挂载到容器中）。

如果修改了 `Dockerfile`，需要重新构建镜像：

```bash
docker compose up -d --build
```

---
Enjoy your writing! ✍️
