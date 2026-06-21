# 标签过滤功能集成说明

## 概述

为 Mizuki WordPress 主题的四个页面模板（友链、项目、技能、时间线）集成了统一的标签过滤功能。

## 实现的文件

### 1. JavaScript 功能
- **文件**: `assets/js/filter-handler.js`
- **功能**: 统一的标签过滤逻辑处理
- **特性**:
  - 支持多个过滤容器独立工作
  - 支持逗号分隔的多标签匹配
  - 显示/隐藏无结果提示
  - 自动初始化和重置支持

### 2. CSS 样式
- **文件**: `assets/css/mizuki-filter-tabs.css`
- **功能**: 标签过滤按钮的样式
- **特性**:
  - 与主题风格一致的按钮设计
  - Active 状态突出显示
  - 响应式设计（移动端优化）
  - 过渡动画效果

### 3. 资源加载
- **文件**: `inc/enqueue.php`
- **修改**: 添加 CSS 和 JS 文件的加载
- **位置**: 
  - CSS: 添加到 `$global_css` 数组
  - JS: 在 `mizuki-theme.js` 之后加载

### 4. 模板集成

#### 友链模板 (template-friends.php)
- **过滤属性**: `data-filter-attr="friend-tags"`
- **内容属性**: `data-friend-tags="tag1,tag2"`
- **容器**: `#friends-grid`

#### 项目模板 (template-projects.php)
- **过滤属性**: `data-filter-attr="project-tags"`
- **内容属性**: `data-project-tags="tag1,tag2"`
- **容器**: `#projects-grid`

#### 技能模板 (template-skills.php)
- **过滤属性**: `data-filter-attr="skill-tags"`
- **内容属性**: `data-skill-tags="tag1,tag2"`
- **容器**: `#skills-grid`

#### 时间线模板 (template-timeline.php)
- **过滤属性**: `data-filter-attr="timeline-tags"`
- **内容属性**: `data-timeline-tags="tag1,tag2"`
- **容器**: `#timeline-wrapper`

## HTML 结构

### 标签过滤按钮组
```html
<div class="filter-tabs flex flex-wrap gap-2 mb-6">
    <!-- 全部按钮 -->
    <button class="filter-tabs-item active" 
            data-filter-attr="friend-tags" 
            data-filter-value="all">
        <svg>...</svg>
        <span>全部</span>
        <span class="filter-tabs-count">(10)</span>
    </button>
    
    <!-- 标签按钮 -->
    <button class="filter-tabs-item" 
            data-filter-attr="friend-tags" 
            data-filter-value="tag-slug">
        <svg>...</svg>
        <span>标签名称</span>
        <span class="filter-tabs-count">(5)</span>
    </button>
</div>
```

### 可过滤内容项
```html
<div class="friend-card" data-friend-tags="tag1,tag2">
    <!-- 卡片内容 -->
</div>
```

### 无结果提示
```html
<div id="no-results" class="hidden text-center py-12">
    <p class="text-50">没有找到匹配的友链。</p>
</div>
```

## 工作原理

1. **初始化**:
   - 页面加载时，`filter-handler.js` 自动查找所有 `.filter-tabs` 容器
   - 为每个容器中的按钮添加点击事件监听

2. **过滤逻辑**:
   - 点击标签按钮时，读取 `data-filter-value`
   - 查找所有带有对应 `data-{filter-attr}` 属性的元素
   - 匹配值（支持逗号分隔的多标签）
   - 显示匹配项，隐藏不匹配项

3. **状态管理**:
   - Active 按钮获得 `.active` 类
   - 隐藏的项目获得 `.filtered-out` 类和 `display: none`
   - 根据可见项数量显示/隐藏 `#no-results` 提示

## 标签收集逻辑

每个模板在生成过滤按钮前，都会：
1. 遍历所有查询结果
2. 使用 `get_the_tags()` 获取每个项目的标签
3. 统计每个标签出现的次数
4. 按标签名称字母顺序排序
5. 生成过滤按钮（仅显示实际使用的标签）

## 响应式设计

- **桌面端**: 显示完整按钮（带图标、文字、计数）
- **移动端** (≤768px):
  - 缩小按钮内边距
  - 隐藏计数数字
  - 减小字体大小

## 浏览器兼容性

- 使用纯 JavaScript（无框架依赖）
- 兼容 ES5+
- 支持所有现代浏览器和 IE11+

## 扩展性

如需为新页面添加过滤功能：

1. 在模板中添加 `.filter-tabs` 容器和按钮
2. 为内容项添加 `data-{your-attr}` 属性
3. 确保按钮有正确的 `data-filter-attr` 和 `data-filter-value`
4. 添加 `#no-results` 提示元素
5. 过滤逻辑会自动工作

## 调试

如需手动重新初始化过滤器：
```javascript
window.mizukiInitFilters();
```

## 性能优化

- 使用事件委托减少事件监听器数量
- CSS 过渡动画使用 GPU 加速
- 过滤操作不触发重排（仅修改 display 属性）
