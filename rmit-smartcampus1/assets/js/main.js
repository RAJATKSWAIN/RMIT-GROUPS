const nav = document.querySelector('.navbar');
window.addEventListener('scroll',()=>{ 
  nav.classList.toggle('scrolled', window.scrollY > 50); 
});

const items = document.querySelectorAll('.animate');
const obs = new IntersectionObserver(entries=>{
  entries.forEach(e=>{
    if(e.isIntersecting){
      e.target.classList.add('show');
      obs.unobserve(e.target);
    }
  });
},{threshold:0.15});
items.forEach(i=>obs.observe(i));
