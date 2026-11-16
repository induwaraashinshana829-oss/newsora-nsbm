'use strict';
const timeGreeting = document.querySelector('[data-time-greeting]');
if (timeGreeting) {
 const updateGreeting = () => {
  const hour = new Date().getHours();
  timeGreeting.textContent = hour < 12 ? 'Good morning' : hour < 18 ? 'Good afternoon' : 'Good evening';
 };
 updateGreeting();
 setInterval(updateGreeting, 60000);
 document.addEventListener('visibilitychange', () => {
  if (!document.hidden) updateGreeting();
 });
}
const appRoot = document.querySelector('meta[name="app-root"]')?.content || '/';
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
let toastTimer;
function showToast(message, login) {
 const toast = document.getElementById('toast');
 if (!toast) return;
 toast.replaceChildren(document.createTextNode(message));
 if (login) {const a=document.createElement('a');a.href=login;a.textContent=' Sign in →';toast.append(a);}
 toast.hidden = false;
 clearTimeout(toastTimer);toastTimer=setTimeout(()=>{toast.hidden=true;},5500);
}
document.querySelectorAll('.menu-toggle').forEach(button=>button.addEventListener('click',()=>{
 const open=document.body.classList.toggle('nav-open');button.setAttribute('aria-expanded',String(open));
}));
document.addEventListener('keydown',e=>{if(e.key==='Escape'){document.body.classList.remove('nav-open');document.querySelectorAll('.menu-toggle').forEach(b=>b.setAttribute('aria-expanded','false'));}});
document.addEventListener('click',e=>{if(document.body.classList.contains('nav-open')&&!e.target.closest('.sidebar,.menu-toggle,.public-header')){document.body.classList.remove('nav-open');document.querySelectorAll('.menu-toggle').forEach(b=>b.setAttribute('aria-expanded','false'));}});
document.querySelectorAll('[data-password]').forEach(button=>button.addEventListener('click',()=>{
 const input=button.parentElement.querySelector('input');input.type=input.type==='password'?'text':'password';button.setAttribute('aria-label',input.type==='password'?'Show password':'Hide password');
}));
document.querySelectorAll('[data-bookmark]').forEach(button=>button.addEventListener('click',async()=>{
 button.disabled=true;
 try {
 const response=await fetch(appRoot+'api.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({id:button.dataset.bookmark,csrf:csrfToken})});
 const result=await response.json();
 if(!response.ok){showToast(result.error||'Unable to save article.',result.login);return;}
 document.querySelectorAll('[data-bookmark="'+button.dataset.bookmark+'"]').forEach(b=>{b.classList.toggle('is-saved',result.saved);b.setAttribute('aria-pressed',String(result.saved));b.setAttribute('aria-label',result.saved?'Remove saved article':'Save article');});
 showToast(result.message);
 if(!result.saved&&button.closest('.saved-list')){button.closest('.news-card').remove();const left=document.querySelectorAll('.saved-list .news-card').length;const empty=document.getElementById('saved-empty');if(empty)empty.hidden=left>0;const badge=document.querySelector('.count-badge');if(badge)badge.textContent=Math.max(0,parseInt(badge.textContent,10)-1)+' saved';}
 } catch {showToast('Could not reach the server. Try again.');} finally{button.disabled=false;}
}));
document.querySelector('[data-share]')?.addEventListener('click',async()=>{try{if(navigator.share)await navigator.share({title:document.title,url:location.href});else{await navigator.clipboard.writeText(location.href);showToast('Article link copied.');}}catch(error){if(error.name!=='AbortError')showToast('Copy this article’s URL from the address bar to share.');}});
document.querySelectorAll('[data-slide]').forEach(button=>button.addEventListener('click',()=>{const grid=document.querySelector('.featured-grid');if(!grid)return;if(button.dataset.slide==='1')grid.append(grid.firstElementChild);else grid.prepend(grid.lastElementChild);}));
const titleInput=document.getElementById('article-title');const bodyInput=document.getElementById('article-body');const categoryInput=document.getElementById('article-category');
titleInput?.addEventListener('input',()=>{document.getElementById('preview-title').textContent=titleInput.value||'Your headline will appear here';document.getElementById('title-count').textContent=titleInput.value.length;});
bodyInput?.addEventListener('input',()=>{document.getElementById('preview-body').textContent=bodyInput.value||'Start writing to preview your story.';});
categoryInput?.addEventListener('change',()=>{document.getElementById('preview-category').textContent=categoryInput.value;});
let coverUrl;
document.getElementById('cover-input')?.addEventListener('change',e=>{const file=e.target.files[0];if(!file)return;if(!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>5*1024*1024){showToast('Choose a JPEG, PNG, or WebP image under 5 MB.');e.target.value='';return;}if(coverUrl)URL.revokeObjectURL(coverUrl);coverUrl=URL.createObjectURL(file);document.getElementById('cover-preview').src=coverUrl;document.getElementById('reader-preview-image').src=coverUrl;});
document.querySelectorAll('#subscription-form input[name="categories[]"]').forEach(input=>input.addEventListener('change',()=>{const summary=document.querySelector('[data-category-summary="'+input.value+'"]');if(summary){summary.textContent=input.checked?'Subscribed':'Paused';summary.closest('.panel').classList.toggle('selected',input.checked);}}));

