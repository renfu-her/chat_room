<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
<p><strong>這裡範例是利用主機當socket</strong></p>
<p><strong>不想設定主機參數可以使用<a href="https://pusher.com/" target="_blank" rel="noopener"> pusher </a></strong></p>

<p><strong>修改env&nbsp;</strong></p>
<pre style="box-sizing: border-box; -webkit-font-smoothing: antialiased; background: #2f333d; font-family: Menlo, Monaco, monospace; line-height: 21px; margin-bottom: 1.5em; overflow: auto; padding: 12.3438px 15.4219px; border: 1px solid #292c33; border-radius: 4px; color: #d2d2d2; font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"># .env<br />BROADCAST_DRIVER=pusher
PUSHER_APP_ID={PUSHER_APP_ID}
PUSHER_APP_KEY={PUSHER_APP_KEY}
PUSHER_APP_SECRET=</span></span></span></span>{PUSHER_APP_SECRET}</pre>
<p><strong>修改bootstarp.js</strong></p>
<pre style="box-sizing: border-box; -webkit-font-smoothing: antialiased; background: #2f333d; font-family: Menlo, Monaco, monospace; line-height: 21px; margin-bottom: 1.5em; overflow: auto; padding: 12.3438px 15.4219px; border: 1px solid #292c33; border-radius: 4px; color: #d2d2d2; font-size: 14px;"><span style="color: #99968b; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"># /resources/js/bootstarp.js
import Echo from 'laravel-echo'
window.Pusher = require('pusher-js');
window.Echo = new Echo({
    broadcaster: 'pusher',
    encrypted: false,
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true
});</span></span></pre>
<p><strong>安裝套件</strong></p>
<pre style="box-sizing: border-box; -webkit-font-smoothing: antialiased; background: #2f333d; font-family: Menlo, Monaco, monospace; line-height: 21px; margin-bottom: 1.5em; overflow: auto; padding: 12.3438px 15.4219px; border: 1px solid #292c33; border-radius: 4px; color: #d2d2d2; font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;">composer install
php artisan migrate
npm install
npm run dev
</span></span></span></span></span></span></span></span></pre>
<p><strong>啟動websocket(預設port:6001)</strong></p>
<pre style="box-sizing: border-box; -webkit-font-smoothing: antialiased; background: #2f333d; font-family: Menlo, Monaco, monospace; line-height: 21px; margin-bottom: 1.5em; overflow: auto; padding: 12.3438px 15.4219px; border: 1px solid #292c33; border-radius: 4px; color: #d2d2d2; font-size: 14px;"><span style="color: #d2d2d2; font-family: Menlo, Monaco, monospace;"><span style="font-size: 14px;"># 預設port:6001<br />php artisan websockets:serve<br />//</span></span>php artisan websockets:serve --port=6001</pre>
<p><strong>範例畫面</strong></p>
<p><a href="https://online.usongrat.tw/" title="demo" target="_blank" rel="noopener"><img src="https://roy.usongrat.tw/storage/images/2022/03/14/messageImage_1647235194338.jpg" alt="範例" width="1168" height="666" /></a></p>
<p><b>github範本 :</b></p>
<p><b>&nbsp;<a href="https://github.com/cc711612/chat_room" title="github" target="_blank" rel="noopener">https://github.com/cc711612/chat_room</a></b></p>
<p><b>範本 :</b></p>
<p><b>&nbsp;<a href="https://chat.usongrat.tw" title="範例" target="_blank" rel="noopener">https://chat.usongrat.tw</a></b></p>
<p><strong>參考文獻:</strong></p>
<p><b><a href="https://beyondco.de/docs/laravel-websockets/getting-started/introduction" title="參考文獻">https://beyondco.de/docs/laravel-websockets/getting-started/introduction</a><a href="https://beyondco.de/docs/laravel-websockets/getting-started/introduction" target="_blank" rel="noopener"></a></b></p>
<p><a href="https://learnku.com/docs/laravel/8.x/broadcasting/9388#presence-channels" target="_blank" title="參考文獻" rel="noopener"><b>https://learnku.com/docs/laravel/8.x/broadcasting/9388#presence-channels</b></a></p>
