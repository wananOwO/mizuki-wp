#!/usr/bin/env python3
"""
创建 GitHub release 并上传 zip 资产。

用法:
    python create-release.py
"""
import os
import sys
import json
import urllib.request
import urllib.error

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
TOKEN = os.environ.get('GH_TOKEN', '')
OWNER = 'wananOwO'
REPO = 'mizuki-wp'
TAG = 'v1.2.0'
ZIP_PATH = os.path.join(ROOT, 'mizuki-wp-theme.zip')

RELEASE_BODY = """## v1.2.0 上游对齐与修复

### 显示与布局

- 显示设置继续对齐上游统一面板,桌面端集中管理主题色、壁纸和布局
- 全屏模式首屏直接显示文章卡片,网格模式隐藏右侧栏
- 新增叠加壁纸模式,支持壁纸不透明度、模糊和卡片不透明度
- 移动端不显示或应用壁纸、叠加效果与布局切换

### WordPress 与交互

- 在「Mizuki 主题」后台增加可编辑的网站公告
- 恢复「我的 / 关于 / 更多」悬停子菜单,保留键盘与点击状态
- 修正网格卡片标题蓝色装饰条对齐
- 实际加载主题打包的 Zen Maru Gothic 与 Loli 字体
- 修正文章卡片的 Unicode 字数统计和设置按钮 `aria-expanded`

### 验证与打包

- 使用原生 Windows PHP、WordPress 与 SQLite 完成安装和前端回归,不依赖 Docker
- 使用 `tools/pack-theme.mjs` 生成并校验单一 `mizuki-wp/` 根目录的安装包

## 安装

下载 `mizuki-wp-theme.zip`,在 WordPress 后台「外观 > 主题 > 添加 > 上传主题」中上传安装。
"""


def api_request(url, method='GET', data=None, token=None, content_type='application/json'):
    """发送 GitHub API 请求。"""
    headers = {
        'Authorization': f'Bearer {token}',
        'Accept': 'application/vnd.github+json',
        'X-GitHub-Api-Version': '2022-11-28',
    }
    if content_type:
        headers['Content-Type'] = content_type

    body = None
    if data is not None:
        if isinstance(data, (dict, list)):
            body = json.dumps(data).encode('utf-8')
        elif isinstance(data, bytes):
            body = data
        else:
            body = data.encode('utf-8')

    req = urllib.request.Request(url, data=body, headers=headers, method=method)
    try:
        with urllib.request.urlopen(req, timeout=60) as resp:
            content = resp.read().decode('utf-8')
            return resp.status, json.loads(content) if content else {}
    except urllib.error.HTTPError as e:
        content = e.read().decode('utf-8')
        try:
            return e.code, json.loads(content)
        except json.JSONDecodeError:
            return e.code, {'raw': content}
    except Exception as e:
        return 0, {'error': str(e)}


def main():
    if not TOKEN:
        print('错误: 未设置 GH_TOKEN 环境变量')
        return 1

    if not os.path.isfile(ZIP_PATH):
        print(f'错误: zip 文件不存在: {ZIP_PATH}')
        return 1

    # 1. 创建 release
    print(f'创建 release {TAG}...')
    status, resp = api_request(
        f'https://api.github.com/repos/{OWNER}/{REPO}/releases',
        method='POST',
        data={
            'tag_name': TAG,
            'target_commitish': 'main',
            'name': f'{TAG} - Mizuki 上游设计与交互对齐',
            'body': RELEASE_BODY,
            'draft': False,
            'prerelease': False,
        },
        token=TOKEN,
    )

    if status != 201:
        print(f'创建 release 失败 (HTTP {status}):')
        print(json.dumps(resp, indent=2, ensure_ascii=False))
        # 如果已存在,尝试获取现有 release
        if status == 422:
            print('Release 可能已存在,尝试获取...')
            status, resp = api_request(
                f'https://api.github.com/repos/{OWNER}/{REPO}/releases/tags/{TAG}',
                method='GET',
                token=TOKEN,
            )
            if status != 200:
                print(f'获取现有 release 失败 (HTTP {status})')
                return 1
        else:
            return 1

    release_id = resp.get('id')
    release_url = resp.get('html_url', '')
    print(f'Release ID: {release_id}')
    print(f'Release URL: {release_url}')

    if not release_id:
        print('错误: 未获取到 release ID')
        return 1

    # 2. 上传 zip 资产
    print(f'\n上传 {os.path.basename(ZIP_PATH)} ({os.path.getsize(ZIP_PATH) / 1024 / 1024:.2f} MB)...')

    with open(ZIP_PATH, 'rb') as f:
        zip_data = f.read()

    upload_url = f'https://uploads.github.com/repos/{OWNER}/{REPO}/releases/{release_id}/assets?name=mizuki-wp-theme.zip'
    status, resp = api_request(
        upload_url,
        method='POST',
        data=zip_data,
        token=TOKEN,
        content_type='application/zip',
    )

    if status != 201:
        print(f'上传资产失败 (HTTP {status}):')
        print(json.dumps(resp, indent=2, ensure_ascii=False))
        return 1

    asset_url = resp.get('browser_download_url', '')
    print(f'资产上传成功!')
    print(f'下载 URL: {asset_url}')
    print(f'\nRelease 页面: {release_url}')

    return 0


if __name__ == '__main__':
    sys.exit(main())
