/**
 * scroll-position-fix.js
 * ไฟล์นี้ใช้แก้ปัญหาการเลื่อนหน้าเว็บอัตโนมัติเมื่อมีการเลือก select หรือการอัพเดต Livewire components
 */
document.addEventListener('DOMContentLoaded', function() {
    // เก็บตำแหน่ง scroll
    let lastScrollPosition = 0;
    
    // เมื่อมีการส่งคำขอ Livewire
    document.addEventListener('livewire:navigating', function() {
        lastScrollPosition = window.scrollY;
    });
    
    // เมื่อมีการอัพเดต component
    document.addEventListener('livewire:navigated', function() {
        // คืนค่าตำแหน่ง scroll หลังจากอัพเดต
        window.scrollTo({
            top: lastScrollPosition,
            behavior: 'auto' // ไม่ใช้ smooth เพื่อป้องกันการเห็นการเลื่อน
        });
    });
    
    // แก้ไขสำหรับ select ทุกอันในหน้า
    document.querySelectorAll('select[wire\\:model], select[wire\\:model\\.live]').forEach(function(select) {
        select.addEventListener('change', function(e) {
            // เก็บตำแหน่ง scroll ก่อนที่จะมีการอัพเดต
            lastScrollPosition = window.scrollY;
            
            // ตั้งเวลาเพื่อคืนค่าตำแหน่งหลังจากที่ Livewire ได้อัพเดตแล้ว
            setTimeout(function() {
                window.scrollTo({
                    top: lastScrollPosition,
                    behavior: 'auto'
                });
            }, 100); // รอสักครู่เพื่อให้ Livewire ได้อัพเดตหน้าเว็บ
        });
    });
});

// แก้ไขสำหรับ Livewire โดยเฉพาะ
document.addEventListener('livewire:init', () => {
    let scrollPositions = {};
    
    // เก็บตำแหน่งก่อนอัพเดต
    Livewire.hook('request', ({ component, commit }) => {
        if (component && component.id) {
            scrollPositions[component.id] = window.scrollY;
        }
    });
    
    // คืนค่าหลังอัพเดต
    Livewire.hook('response', ({ component, commit }) => {
        if (component && component.id && scrollPositions[component.id] !== undefined) {
            window.scrollTo({
                top: scrollPositions[component.id],
                behavior: 'auto'
            });
            delete scrollPositions[component.id];
        }
    });
});
