class h{constructor(){this.visibleBlocks=new Set,this.pendingThemeUpdate=null,this.codeBlockObserver=null,this.hideCodeBlocksDuringTransition=!0,this.initFromConfig(),this.isOptimizing=!1,this.heavySelectors=[".float-panel","#navbar",".music-player","#mobile-toc-panel","#nav-menu-panel","#search-panel",".dropdown-content",".widget",".post-card",".custom-md"],this.init()}init(){this.initFromConfig(),this.initCodeBlockOptimization(),this.interceptThemeSwitch(),this.applyCodeBlockTransitionBehavior(),this.setupSwupHooks(),document.dispatchEvent(new CustomEvent("themeOptimizerReady"))}setupSwupHooks(){const t=()=>window.swup?(window.swup.hooks.on("page:view",()=>{setTimeout(()=>{this.observeCodeBlocks(),this.applyCodeBlockTransitionBehavior(),this.forceApplyThemeTransitionStyles()},100)}),window.swup.hooks.on("content:replace",()=>{setTimeout(()=>{this.applyCodeBlockTransitionBehavior(),this.forceApplyThemeTransitionStyles()},50)}),!0):!1;if(!t()){document.addEventListener("swup:enable",()=>{t()});const e=setInterval(()=>{t()&&clearInterval(e)},100);setTimeout(()=>{clearInterval(e)},2e3)}}forceApplyThemeTransitionStyles(){const t=document.querySelectorAll(".expressive-code");t.forEach(i=>{this.hideCodeBlocksDuringTransition?i.classList.add("hide-during-transition"):i.classList.remove("hide-during-transition"),i.offsetWidth}),document.documentElement.classList.contains("is-theme-transitioning")?t.forEach(i=>{i.classList.contains("hide-during-transition")&&(i.style.setProperty("content-visibility","hidden","important"),i.style.setProperty("opacity","0.99","important"))}):t.forEach(i=>{i.style.removeProperty("content-visibility"),i.style.removeProperty("opacity")})}initFromConfig(){try{const t=document.getElementById("config-carrier");t&&t.dataset.hideCodeBlocksDuringTransition!==void 0&&(this.hideCodeBlocksDuringTransition=t.dataset.hideCodeBlocksDuringTransition==="true")}catch{this.hideCodeBlocksDuringTransition=!0}}applyCodeBlockTransitionBehavior(){document.querySelectorAll(".expressive-code").forEach(e=>{this.hideCodeBlocksDuringTransition?e.classList.add("hide-during-transition"):e.classList.remove("hide-during-transition")}),this.updateTempStyleSheet()}updateTempStyleSheet(){if(this.tempStyleSheet){let t=this.tempStyleSheet.textContent;t.includes(".is-theme-transitioning .expressive-code")||(t+=`
`+`.is-theme-transitioning .expressive-code {
        content-visibility: hidden !important;
        /* 避免闪烁 */
        opacity: 0.99;
      }`+`
`+`.is-theme-transitioning .expressive-code:not(.hide-during-transition) {
        /* 保持代码块可见，但禁用过渡效果 */
        content-visibility: visible !important;
        opacity: 1 !important;
      }`,this.tempStyleSheet.textContent=t)}}initCodeBlockOptimization(){this.codeBlockObserver=new IntersectionObserver(t=>{t.forEach(e=>{e.isIntersecting?(this.visibleBlocks.add(e.target),this.pendingThemeUpdate&&this.applyThemeToBlock(e.target,this.pendingThemeUpdate)):this.visibleBlocks.delete(e.target)})},{rootMargin:"50px 0px",threshold:.01}),this.observeCodeBlocks(),this.setupThemeListener(),window.swup&&window.swup.hooks.on("page:view",()=>{setTimeout(()=>this.observeCodeBlocks(),100)})}observeCodeBlocks(){this.visibleBlocks.clear(),requestAnimationFrame(()=>{document.querySelectorAll(".expressive-code").forEach(e=>{this.codeBlockObserver.observe(e),this.hideCodeBlocksDuringTransition?e.classList.add("hide-during-transition"):e.classList.remove("hide-during-transition")})})}setupThemeListener(){new MutationObserver(e=>{for(const i of e)if(i.type==="attributes"&&i.attributeName==="data-theme"){const s=document.documentElement.getAttribute("data-theme");this.handleThemeChange(s);break}}).observe(document.documentElement,{attributes:!0,attributeFilter:["data-theme"]})}handleThemeChange(t){this.pendingThemeUpdate=t;const e=Array.from(this.visibleBlocks);e.length!==0&&this.batchUpdateBlocks(e,t)}batchUpdateBlocks(t,e){let s=0;const n=()=>{const o=t.slice(s,s+3);requestAnimationFrame(()=>{o.forEach(r=>{this.applyThemeToBlock(r,e)}),s+=3,s<t.length&&setTimeout(n,0)})};n()}applyThemeToBlock(t,e){t.dataset.themeUpdated=e}interceptThemeSwitch(){new MutationObserver(e=>{for(const i of e)if(i.type==="attributes"&&i.attributeName==="class"&&i.target===document.documentElement){const s=document.documentElement.classList,n=s.contains("is-theme-transitioning"),o=s.contains("use-view-transition");n&&!this.isOptimizing?this.optimizeThemeSwitch(o):!n&&this.isOptimizing&&this.restoreAfterThemeSwitch(o)}}).observe(document.documentElement,{attributes:!0,attributeFilter:["class"]})}optimizeThemeSwitch(t=!1){this.isOptimizing=!0,this.useViewTransition=t,!t&&(this.disableHeavyAnimations(),this.hideOffscreenHeavyElements(),this.forceCompositing())}disableHeavyAnimations(){this.tempStyleSheet||(this.tempStyleSheet=document.createElement("style"),this.tempStyleSheet.id="theme-optimizer-temp",document.head.appendChild(this.tempStyleSheet)),this.tempStyleSheet.textContent=`
      /* 临时禁用重型元素的过渡和动画 */
      .is-theme-transitioning .float-panel:not(.float-panel-closed),
      .is-theme-transitioning .music-player,
      .is-theme-transitioning .widget,
      .is-theme-transitioning .post-card,
      .is-theme-transitioning #navbar *,
      .is-theme-transitioning .dropdown-content,
      .is-theme-transitioning .custom-md * {
        transition: none !important;
        animation: none !important;
      }
      
      /* 强制隔离渲染上下文 */
      .is-theme-transitioning .float-panel,
      .is-theme-transitioning .post-card,
      .is-theme-transitioning .widget {
        contain: layout style paint !important;
      }
      
      /* 隐藏装饰性元素 */
      .is-theme-transitioning .gradient-overlay,
      .is-theme-transitioning .decoration,
      .is-theme-transitioning .animation-element {
        visibility: hidden !important;
      }
      
      /* 在主题切换期间临时隐藏代码块以提升性能 */
      /* 这个行为可以通过配置文件中的 expressiveCodeConfig.hideDuringThemeTransition 控制 */
      .is-theme-transitioning .expressive-code {
        content-visibility: hidden !important;
        /* 避免闪烁 */
        opacity: 0.99;
      }
      
      /* 当禁用隐藏代码块功能时（通过JavaScript动态控制） */
      .is-theme-transitioning .expressive-code:not(.hide-during-transition) {
        /* 保持代码块可见，但禁用过渡效果 */
        content-visibility: visible !important;
        opacity: 1 !important;
      }
      
      /* 确保打开的TOC面板在主题切换期间保持可点击 */
      .is-theme-transitioning .float-panel:not(.float-panel-closed) {
        pointer-events: auto !important;
      }
    `}hideOffscreenHeavyElements(){const t=window.innerHeight,e=window.scrollY;this.hiddenElements=[],this.heavySelectors.forEach(i=>{document.querySelectorAll(i).forEach(n=>{const o=n.getBoundingClientRect(),r=o.top+e;if(r+o.height<e-200||r>e+t+200){const a=n.style.contentVisibility;n.style.contentVisibility="hidden",this.hiddenElements.push({element:n,originalVisibility:a})}})})}forceCompositing(){const t=document.querySelectorAll(`
      .expressive-code,
      .post-card,
      .widget,
      #navbar
    `);this.compositedElements=[],t.forEach(e=>{const i=e.style.transform;e.style.transform="translateZ(0)",e.style.willChange="transform",this.compositedElements.push({element:e,original:i})})}restoreAfterThemeSwitch(t=!1){if(this.isOptimizing=!1,t){this.useViewTransition=!1;return}requestAnimationFrame(()=>{requestAnimationFrame(()=>{this.tempStyleSheet&&this.tempStyleSheet.parentNode&&(this.tempStyleSheet.remove(),this.tempStyleSheet=null),this.hiddenElements&&(this.hiddenElements.forEach(({element:e,originalVisibility:i})=>{e.style.contentVisibility=i||""}),this.hiddenElements=null),this.compositedElements&&(this.compositedElements.forEach(({element:e,original:i})=>{e.style.transform=i||"",e.style.willChange=""}),this.compositedElements=null)})})}destroy(){this.codeBlockObserver&&this.codeBlockObserver.disconnect(),this.visibleBlocks.clear()}}const c=new h;window.themeOptimizer=c;
