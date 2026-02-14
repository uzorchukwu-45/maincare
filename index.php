<?php include 'header.php'; ?>

<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f4f8; margin: 0; padding: 0; color: #333;">

<div style="max-width: 900px; margin: 40px auto; padding: 40px; background-color: #ffffff; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center;">
    <span style="color: #00bcd4; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; font-size: 0.9rem;">More Than Just a Reminder</span>
    <h2 style="color: #1a237e; font-size: 2.5rem; margin-top: 10px; font-family: 'Poppins', sans-serif;">The Heart Behind DoseCare</h2>
    
    <p style="font-size: 1.1rem; line-height: 1.8; color: #555; margin-top: 20px;">
        At <strong style="color: #1a237e;">DoseCare</strong>, we believe that recovering from illness shouldn't feel like a second job. We know that behind every medication schedule is a person—a parent, a grandparent, or a friend—who just wants to get back to feeling like themselves.
    </p>

    <div style="background-color: #e8eaf6; padding: 30px; border-left: 5px solid #1a237e; border-radius: 8px; font-style: italic; font-size: 1.2rem; color: #1a237e; margin: 30px 0; line-height: 1.6;">
        "DoseCare isn't just about pills and schedules; it's about <strong style="color: #00bcd4;">peace of mind</strong>. It’s the quiet confidence that comes from knowing you’re doing exactly what you need to do to get better."
    </div>

    <p style="font-size: 1.1rem; line-height: 1.8; color: #555;">
        We’ve built a system that speaks the language of care. By simplifying complex dosages into gentle nudges, we remove the "pill fatigue" and replace it with a clear, achievable path to wellness. With DoseCare, you aren't just managing an illness—you're nurturing your health.
    </p>

    <div style="margin-top: 40px; font-weight: 600; color: #1a237e; border-top: 1px solid #eee; padding-top: 20px;">
        DoseCare: Designed with Science, Delivered with Heart <span style="color: #ff4081;">❤</span>
    </div>
</div>

<hr style="border: 0; height: 1px; background: #ddd; margin: 50px 0;">

<div style="text-align: center; margin-bottom: 20px;">
    <h3 style="color: #1a237e;">Our Medical Support Team</h3>
    <p style="color: #777;">Scroll left or right to view our personalized care options</p>
</div>



<div id="cardContainer" style="display: flex; overflow-x: auto; gap: 25px; padding: 20px 50px; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
    
    <?php 
    // Array to simulate your nurse images
    $nurses = ['nurse.jpg', 'nurse2.jpg', 'nurse1.jpg', 'nurse4.jpg', 'nurse3.jpg'];
    foreach ($nurses as $img): 
    ?>
    <article onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.1)';" 
             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.05)';" 
             style="flex: 0 0 300px; background: white; border-radius: 12px; overflow: hidden; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0,0,0,0.05); cursor: pointer;">
        
        <img src="./images/<?php echo $img; ?>" alt="Healthcare Professional" style="width: 100%; height: 200px; object-fit: cover;">
        
        <div style="padding: 20px;">
            <span style="background: #e0f7fa; color: #00838f; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">Patient Support</span>
            <h3 style="margin: 15px 0 10px 0; color: #1a237e; font-size: 1.25rem;">Personalized Care</h3>
            <p style="font-size: 0.9rem; color: #666; line-height: 1.5; margin-bottom: 20px;">
                Receive tailored medication insights and real-time support from the DoseCare medical team.
            </p>
            <a href="#" style="display: inline-block; padding: 10px 20px; background-color: #1a237e; color: white; text-decoration: none; border-radius: 5px; font-size: 0.85rem; transition: background 0.3s;">Learn More</a>
        </div>
    </article>
    <?php endforeach; ?>

</div>

<hr style="border: 0; height: 1px; background: #ddd; margin: 50px 0;">

<script>
    // 1. Auto-scroll functionality for the cards
    const container = document.getElementById('cardContainer');
    let scrollAmount = 0;
    let isHovered = false;

    container.addEventListener('mouseenter', () => isHovered = true);
    container.addEventListener('mouseleave', () => isHovered = false);

    function autoScroll() {
        if (!isHovered) {
            scrollAmount += 1;
            if (scrollAmount >= container.scrollWidth - container.clientWidth) {
                scrollAmount = 0;
            }
            container.scrollLeft = scrollAmount;
        }
    }

    // Run auto-scroll every 30ms for a smooth effect
    setInterval(autoScroll, 30);

    // 2. Interactive Learn More buttons
    const buttons = document.querySelectorAll('article a');
    buttons.forEach(btn => {
        btn.onmouseover = () => btn.style.backgroundColor = '#00bcd4';
        btn.onmouseout = () => btn.style.backgroundColor = '#1a237e';
    });
</script>

</body>

<?php include 'footer.php'; ?>
