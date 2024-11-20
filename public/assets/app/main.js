'use strict';
self.addEventListener('DOMContentLoaded', onDOMContentLoaded);
const select = (value = HTMLElement | null) => document.querySelector(value);
const selectAll = (value = HTMLElement | null) => document.querySelectorAll(value);
const selectID = (value = HTMLElement | null) => document.getElementById(value);
const template = (value = ``) => { return value }
function onDOMContentLoaded() {
    console.log('DOMContentLoaded');
    setInterval(() => {
        selectID('date-time').innerHTML = new Date().toLocaleTimeString('en-US', {
            weekday: 'short',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
            hour12: true,
        });
    }, 1000);

    const SIDEBAR = select('.sidebar');
    const nav_heigth = select('header ul');
    const SIDEBAR_TOGGLE = select('.sidebar-toggle');
    select('.main').style.top = nav_heigth.offsetHeight + 'px';
    SIDEBAR.style.top = nav_heigth.clientHeight + 'px';
    const currentUrl = window.location.href;
    const stripURL = currentUrl.replace(/(#.*|[\?].*)/g, '');
    const TargetLink = selectAll('.sidebar ul>li.nav-menu a.nav-menu-link');
    [...TargetLink].forEach(e => {
        if (e.href == stripURL) {
            e.classList.add('active');
            e.parentElement.classList.add('active');
        } else {
            e.classList.remove('active');
            e.parentElement.classList.remove('active');
        }
    });
    $('.select2').select2({
        width: '100%',
        placeholder: '',
    });    
}