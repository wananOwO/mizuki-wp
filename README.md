# Mizuki for WordPress

> 将 [Mizuki](https://github.com/LyraVoid/Mizuki)（Astro 6 博客主题，作者 Matsuzaka Yuki）移植为 WordPress 主题的非官方项目。

[![License: Apache-2.0](https://img.shields.io/badge/License-Apache%202.0-blue.svg)](LICENSE-APACHE)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE-MIT)
[![GitHub release](https://img.shields.io/github/v/release/wananOwO/mizuki-wp)](https://github.com/wananOwO/mizuki-wp/releases)
[![GitHub stars](https://img.shields.io/github/stars/wananOwO/mizuki-wp?style=social)](https://github.com/wananOwO/mizuki-wp/stargazers)
[![GitHub issues](https://img.shields.io/github/issues/wananOwO/mizuki-wp)](https://github.com/wananOwO/mizuki-wp/issues)

## ✨ 特性

- 🎨 **完整移植原版视觉设计** - 保持 Mizuki 原版的优雅外观和流畅动画
- 📱 **响应式布局** - 完美适配桌面、平板和移动设备
- 🌙 **深色模式** - 内置日间/夜间主题切换
- ⚡ **性能优化** - 按需加载资源，减少 33% 加载量
- 🎯 **特色功能页面**:
  - **时间线** - 独立的成长记录系统（教育/工作/项目/成就）
  - **项目展示** - 作品集管理，支持分类筛选
  - **技能树** - 技能熟练度可视化
  - **友链** - 友情链接管理
  - **追番** - 动漫观看记录
  - **日记** - 图片日记
  - **相册** - 照片集合
- 🏷️ **智能分类系统** - 所有特色功能支持自定义分类和筛选
- 🔍 **动态筛选** - JavaScript 驱动的实时内容过滤
- 🎭 **Iconify 图标支持** - 海量图标库，时间线完全复刻原版设计

## 📦 安装

### 方法 1: 从 GitHub 下载

1. 下载最新版本的主题 ZIP 包
2. 登录 WordPress 后台 → 外观 → 主题 → 添加
3. 上传 ZIP 文件并激活

### 方法 2: Git Clone

```bash
cd /path/to/wordpress/wp-content/themes/
git clone https://github.com/YOUR_USERNAME/mizuki-wp.git
```

然后在 WordPress 后台激活主题。

## 🚀 快速开始

### 1. 基础配置

激活主题后，访问 **WordPress 后台 → Mizuki 主题**，你会看到以下菜单：

- 时间线
- 项目
- 技能
- 友链
- 追番
- 日记
- 相册

### 2. 创建特色页面

为每个功能创建页面并分配对应模板：

1. 页面 → 新建页面
2. 右侧"页面属性"选择对应模板（如"时间线"）
3. 发布页面

### 3. 添加内容

在 **Mizuki 主题** 菜单下添加时间线条目、项目、技能等内容。

### 4. 自定义分类

访问 **外观 → 自定义 → 内容分类管理**，自定义各功能的分类显示名和图标。

## 📖 使用文档

### 时间线功能

时间线是记录个人成长历程的独立系统（不同于文章归档）。支持：

- **4 种类型**: 教育、工作、项目、成就
- **丰富字段**: 描述、日期范围、地点、组织、职位、技能标签、成就列表、相关链接
- **视觉效果**: 
  - 左侧垂直时间轴线
  - 圆形节点（hover 放大）
  - 进行中项目的脉冲动画
  - Iconify 图标支持

**添加时间线条目**:

1. Mizuki 主题 → 时间线 → 添加时间线条目
2. 填写标题、描述、日期等信息
3. 右侧"类型"面板选择分类
4. 发布

### 项目管理

展示作品集，支持：

- 项目链接和源码链接
- 技术栈标签
- 项目状态（进行中/已完成/暂停）
- 分类筛选（Web/移动端/桌面端等）

### 技能树

可视化展示技能熟练度：

- 熟练度百分比（0-100）
- 自定义图标（支持 Devicon）
- 分类管理（前端/后端/数据库/工具）

## 🛠️ 性能优化

相比初始移植版本，当前版本实现了：

- ✅ 按需加载 CSS（减少 ~128KB）
- ✅ 特色页面 JS 按需加载
- ✅ 图片懒加载
- ✅ 资源分组（全局/文章页/特色页）
- ✅ 减少 33% 总资源量
- ✅ 50-100% 性能提升

详见 [`docs/PERFORMANCE-*.md`](docs/)

## 🤝 贡献

欢迎贡献代码、报告问题或提出建议！

1. Fork 本仓库
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 提交 Pull Request

## 📝 许可证

本项目采用 **双许可证**:

- [Apache License 2.0](LICENSE-APACHE)
- [MIT License](LICENSE-MIT)

你可以选择其中任意一种许可证使用本项目。

### 与上游的关系

本项目基于 [Mizuki](https://github.com/LyraVoid/Mizuki)（Astro 6 主题，作者 Matsuzaka Yuki）移植到 WordPress 平台。

**重要声明（Apache-2.0 §4(b)）**:
- 本项目对原始 Astro 模板、样式和脚本进行了大量改写和重组
- 架构从静态站点生成器（Astro）转换为动态 CMS（WordPress）
- 行为和实现与上游 Astro 版本存在显著差异
- 所有修改均标注"基于 Mizuki 移植"

**版权**:
- 原始 Mizuki 主题: Copyright © Matsuzaka Yuki
- WordPress 移植部分: Copyright © 2026 Mizuki WP Contributors

## 🙏 致谢

- [Matsuzaka Yuki](https://github.com/LyraVoid) - Mizuki 原作者
- 所有为本项目做出贡献的开发者

## 📧 支持

- 🐛 报告 Bug: [GitHub Issues](https://github.com/wananOwO/mizuki-wp/issues)
- 💡 功能建议: [GitHub Discussions](https://github.com/wananOwO/mizuki-wp/discussions)
- 📖 原版主题: [Mizuki by LyraVoid](https://github.com/LyraVoid/Mizuki)

---

**注意**: 这是一个非官方移植项目，与原 Mizuki 主题作者无关联。如果你喜欢这个设计，请访问并支持[原版项目](https://github.com/LyraVoid/Mizuki)。
