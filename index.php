<?php
session_start();
require_once 'core/Database.php';

$success_msg = '';
$error_msg = '';

// Contact Form Backend Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_contact'])) {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $interest = htmlspecialchars($_POST['interest'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');
    
    $full_message = "Interest Area: " . $interest . "\n\n" . $message;

    if (!empty($name) && !empty($phone) && !empty($email)) {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $phone, $full_message])) {
                $success_msg = "Thank you! Your request has been securely logged in our system.";
            } else {
                $error_msg = "Something went wrong. Please try again.";
            }
        } catch (Exception $e) {
            $error_msg = "Database error: " . $e->getMessage();
        }
    } else {
        $error_msg = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Foundation Trust | Empowering Communities & Learning</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            margin: 0; 
            overflow-x: hidden; 
            font-family: 'Inter', sans-serif; 
            background-color: #030303; 
            color: #ffffff; 
        }
        
        .text-gradient { 
            background: linear-gradient(90deg, #00f2ff, #7000ff); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
        }
        .text-gradient-alt {
            background: linear-gradient(135deg, #e0e0e0, #ffffff);
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent;
        }

        .glass-nav {
            background: rgba(3, 3, 3, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .glass-panel {
            background: rgba(15, 15, 15, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        .glass-card {
            background: rgba(20, 20, 20, 0.5);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            transition: all 0.4s ease;
        }
        
        .glass-card:hover {
            background: rgba(30, 30, 30, 0.7);
            border-color: rgba(0, 242, 255, 0.4);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px -10px rgba(0, 242, 255, 0.2);
        }

        .btn-primary { 
            background: linear-gradient(90deg, #7000ff, #00f2ff); 
            background-size: 200% auto;
            transition: 0.5s; 
        }
        .btn-primary:hover { 
            background-position: right center;
            transform: scale(1.02);
            box-shadow: 0 0 20px rgba(0, 242, 255, 0.4);
        }
        
        .btn-outline {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(0, 242, 255, 0.5);
            backdrop-filter: blur(4px);
            transition: 0.3s;
        }
        .btn-outline:hover {
            background: rgba(0, 242, 255, 0.1);
            border-color: #00f2ff;
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #030303; }
        ::-webkit-scrollbar-thumb { background: #7000ff; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #00f2ff; }

        .glass-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            transition: 0.3s;
            outline: none;
        }
        .glass-input:focus {
            border-color: #00f2ff;
            background: rgba(0, 242, 255, 0.05);
        }
    </style>
</head>
<body>

    <div id="canvas-container" style="position: fixed; top: 0; left: 0; width: 100%; height: 100vh; z-index: -1; pointer-events: none; background: #030303;"></div>

    <div id="main-site" style="position: relative; z-index: 10;">
        
        <!-- Navigation -->
        <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
            <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#7000ff] to-[#00f2ff] flex items-center justify-center shadow-[0_0_15px_rgba(0,242,255,0.3)]">
                        <i class="fas fa-dove text-white text-xl"></i>
                    </div>
                    <div>
                        <div class="font-bold text-xl tracking-wide leading-tight">DYNAMIC FOUNDATION</div>
                        <div class="text-[10px] text-gray-400 tracking-widest uppercase">Trust</div>
                    </div>
                </div>
                
                <div class="hidden md:flex space-x-8 text-sm font-medium text-gray-300">
                    <a href="#home" class="hover:text-[#00f2ff] transition-colors">Home</a>
                    <a href="#about" class="hover:text-[#00f2ff] transition-colors">About</a>
                    <a href="#programs" class="hover:text-[#00f2ff] transition-colors">Programs</a>
                    <a href="#courses" class="text-[#00f2ff] font-bold tracking-wide border-b border-[#00f2ff] pb-1 transition-colors">Courses <span class="bg-red-500 text-white text-[9px] px-1 rounded ml-1">NEW</span></a>
                    <a href="#contact" class="hover:text-[#00f2ff] transition-colors">Contact</a>
                </div>

                <div class="hidden md:flex items-center gap-4">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="user/dashboard.php" class="text-sm font-semibold text-[#00f2ff] hover:text-white flex items-center gap-2 mr-3">
                            <i class="fas fa-user-circle"></i> Dashboard
                        </a>
                    <?php else: ?>
                        <a href="user/login.php" class="text-sm font-semibold text-gray-300 hover:text-white flex items-center gap-2 mr-3">
                            <i class="fas fa-sign-in-alt text-[#00f2ff]"></i> Login
                        </a>
                    <?php endif; ?>
                    <a href="donate.php" class="btn-primary text-white px-6 py-2 rounded-full font-bold text-sm tracking-wide shadow-lg">
                        DONATE
                    </a>
                </div>
                
                <button class="md:hidden text-white text-2xl focus:outline-none" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div id="mobile-menu" class="hidden md:hidden glass-panel border-t border-white/10">
                <div class="flex flex-col p-6 space-y-4 text-center">
                    <a href="#home" class="text-gray-300 hover:text-[#00f2ff]">Home</a>
                    <a href="#about" class="text-gray-300 hover:text-[#00f2ff]">About</a>
                    <a href="#programs" class="text-gray-300 hover:text-[#00f2ff]">Programs</a>
                    <a href="#courses" class="text-[#00f2ff] font-bold">Courses <span class="bg-red-500 text-white text-xs px-1 rounded ml-1">NEW</span></a>
                    <a href="#contact" class="text-gray-300 hover:text-[#00f2ff]">Contact</a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="user/dashboard.php" class="text-[#00f2ff] hover:text-white font-bold">Dashboard</a>
                    <?php else: ?>
                        <a href="user/login.php" class="text-gray-300 hover:text-[#00f2ff]">Login</a>
                    <?php endif; ?>
                    <a href="donate.php" class="btn-primary text-white py-3 rounded-full font-bold">DONATE NOW</a>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="home" class="min-h-screen flex flex-col justify-center items-center text-center px-4 pt-20 relative">
            <div class="glass-panel p-2 px-6 rounded-full border border-[#00f2ff]/30 mb-8 inline-flex items-center gap-2 shadow-[0_0_20px_rgba(0,242,255,0.1)]">
                <i class="fas fa-globe-asia text-[#00f2ff]"></i>
                <span class="text-sm font-medium tracking-wider text-gray-200">Learn, Empower & Transform</span>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-tight max-w-5xl text-gradient-alt">
                Empowering Communities, <br>
                <span class="text-gradient">Building Careers</span>
            </h1>
            
            <p class="max-w-2xl text-gray-400 text-lg md:text-xl mb-12 font-light leading-relaxed">
                Join our mission to create a sustainable future through social initiatives, and upgrade your skills with our premium digital courses.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-6 w-full justify-center max-w-md mx-auto sm:max-w-none">
                <a href="#courses" class="btn-primary w-full sm:w-auto px-10 py-4 rounded-full font-bold text-sm tracking-widest uppercase text-white flex justify-center items-center gap-2">
                    Explore Courses <i class="fas fa-graduation-cap"></i>
                </a>
                <a href="donate.php" class="btn-outline w-full sm:w-auto px-10 py-4 rounded-full font-bold text-sm tracking-widest uppercase text-[#00f2ff] flex justify-center items-center gap-2">
                    Donate to NGO
                </a>
            </div>
        </section>

        <!-- Impact Section -->
        <section id="impact" class="py-16 relative z-10 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="glass-panel rounded-2xl p-8 md:p-12 border border-white/10 shadow-[0_0_40px_rgba(112,0,255,0.1)]">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 divide-x divide-white/10">
                        <div class="text-center px-4">
                            <div class="text-4xl md:text-5xl font-bold text-white mb-2 flex justify-center items-center">
                                <span class="counter" data-target="15">0</span><span class="text-[#00f2ff]">+</span>
                            </div>
                            <p class="text-gray-400 font-medium text-xs md:text-sm uppercase tracking-widest">Years Active</p>
                        </div>
                        <div class="text-center px-4">
                            <div class="text-4xl md:text-5xl font-bold text-white mb-2 flex justify-center items-center">
                                <span class="counter" data-target="25000">0</span><span class="text-[#7000ff]">+</span>
                            </div>
                            <p class="text-gray-400 font-medium text-xs md:text-sm uppercase tracking-widest">Lives Impacted</p>
                        </div>
                        <div class="text-center px-4">
                            <div class="text-4xl md:text-5xl font-bold text-white mb-2 flex justify-center items-center">
                                <span class="counter" data-target="5000">0</span><span class="text-[#00f2ff]">+</span>
                            </div>
                            <p class="text-gray-400 font-medium text-xs md:text-sm uppercase tracking-widest">Students Enrolled</p>
                        </div>
                        <div class="text-center px-4">
                            <div class="text-4xl md:text-5xl font-bold text-white mb-2 flex justify-center items-center">
                                <span class="counter" data-target="500">0</span><span class="text-[#7000ff]">+</span>
                            </div>
                            <p class="text-gray-400 font-medium text-xs md:text-sm uppercase tracking-widest">Volunteers</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- (About & Programs remain identical) -->
        <section id="about" class="py-24 relative z-10 px-6">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-16 items-center">
                <div class="md:w-1/2 relative group">
                    <div class="glass-panel p-3 rounded-3xl transform group-hover:-rotate-2 transition duration-500">
                        <div class="relative overflow-hidden rounded-2xl aspect-[4/3]">
                            <img src="https://images.unsplash.com/photo-1593113565694-c6aa89f4facd?auto=format&fit=crop&q=80&w=1000" alt="Children learning" class="w-full h-full object-cover opacity-80 mix-blend-luminosity hover:mix-blend-normal hover:opacity-100 transition duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                        </div>
                    </div>
                    <div class="absolute -bottom-8 -right-8 glass-card p-6 border-[#7000ff]/50 animate-bounce" style="animation-duration: 3s;">
                        <i class="fas fa-heart text-3xl text-[#00f2ff] mb-2"></i>
                        <p class="font-bold text-white tracking-wide">100% Dedicated</p>
                    </div>
                </div>
                
                <div class="md:w-1/2">
                    <h4 class="text-[#00f2ff] font-bold tracking-widest uppercase mb-2 text-sm">Who We Are</h4>
                    <h2 class="text-3xl md:text-5xl font-bold mb-6 text-white leading-tight">Catalysts for <span class="text-gradient">Real Change</span></h2>
                    <p class="text-gray-400 mb-6 text-lg leading-relaxed">
                        Dynamic Foundation Trust is a premier non-profit organization operating at the intersection of human compassion and modern innovation. We believe in providing scalable solutions to grassroots problems.
                    </p>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-4">
                            <div class="mt-1 w-6 h-6 rounded-full bg-[#7000ff]/20 flex items-center justify-center border border-[#7000ff]/50 shrink-0">
                                <i class="fas fa-check text-[#00f2ff] text-xs"></i>
                            </div>
                            <span class="text-gray-300 font-medium">Technology-driven education initiatives.</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="mt-1 w-6 h-6 rounded-full bg-[#7000ff]/20 flex items-center justify-center border border-[#7000ff]/50 shrink-0">
                                <i class="fas fa-check text-[#00f2ff] text-xs"></i>
                            </div>
                            <span class="text-gray-300 font-medium">Sustainable women & child development programs.</span>
                        </li>
                    </ul>
                    <a href="#contact" class="inline-flex items-center gap-2 text-[#00f2ff] font-bold hover:text-white transition-colors border-b border-[#00f2ff] pb-1">
                        Get Involved <i class="fas fa-arrow-right text-sm"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- ================= NEW COURSES SECTION ADDED HERE ================= -->
        <section id="courses" class="py-24 relative z-10 px-6 bg-black/40 border-y border-white/5">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h4 class="text-[#00f2ff] font-bold tracking-widest uppercase mb-2 text-sm">Learn & Grow</h4>
                    <h2 class="text-3xl md:text-5xl font-bold text-white">Premium <span class="text-gradient">Skill Courses</span></h2>
                    <p class="text-gray-400 mt-4 max-w-2xl mx-auto">Master high-demand skills with our industry-expert designed courses. Part of the proceeds goes directly towards supporting our NGO initiatives.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    
                    <!-- Course 1 -->
                    <div class="glass-card p-6 flex flex-col group relative overflow-hidden">
                        <div class="absolute top-4 right-4 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full z-10 shadow-lg">Bestseller</div>
                        <div class="h-48 rounded-xl mb-6 relative overflow-hidden group-hover:scale-105 transition duration-500">
                            <!-- Course Thumbnail -->
                            <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover opacity-70">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end p-4">
                                <span class="bg-[#7000ff]/80 backdrop-blur text-white text-xs px-2 py-1 rounded"><i class="fas fa-video me-1"></i> 45+ Hours Video</span>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Full Stack Web Development</h3>
                        <p class="text-gray-400 text-sm mb-6 flex-grow line-clamp-3">Master frontend & backend development from scratch. Build real-world projects using HTML, CSS, JavaScript, PHP & MySQL.</p>
                        
                        <div class="flex justify-between items-center pt-4 border-t border-white/10 mt-auto">
                            <div>
                                <div class="text-gray-500 text-xs line-through">₹4,999</div>
                                <div class="text-2xl font-bold text-[#00f2ff]">₹999</div>
                            </div>
                            <!-- Ye link agle step me buy_course.php se jodege -->
                            <a href="buy_course.php?course_id=1" class="btn-primary px-6 py-2 rounded-lg font-bold text-sm text-white shadow-[0_0_15px_rgba(0,242,255,0.3)]">Buy Now</a>
                        </div>
                    </div>

                    <!-- Course 2 -->
                    <div class="glass-card p-6 flex flex-col group relative overflow-hidden">
                        <div class="h-48 rounded-xl mb-6 relative overflow-hidden group-hover:scale-105 transition duration-500">
                            <img src="https://images.unsplash.com/photo-1611162617474-5b21e879e113?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover opacity-70">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end p-4">
                                <span class="bg-[#7000ff]/80 backdrop-blur text-white text-xs px-2 py-1 rounded"><i class="fas fa-video me-1"></i> 25+ Hours Video</span>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Digital Marketing Masterclass</h3>
                        <p class="text-gray-400 text-sm mb-6 flex-grow line-clamp-3">Learn SEO, Social Media Ads (Facebook/Instagram), Google Analytics, and Content Marketing to grow any business.</p>
                        
                        <div class="flex justify-between items-center pt-4 border-t border-white/10 mt-auto">
                            <div>
                                <div class="text-gray-500 text-xs line-through">₹2,999</div>
                                <div class="text-2xl font-bold text-[#00f2ff]">₹499</div>
                            </div>
                            <a href="buy_course.php?course_id=2" class="btn-primary px-6 py-2 rounded-lg font-bold text-sm text-white shadow-[0_0_15px_rgba(0,242,255,0.3)]">Buy Now</a>
                        </div>
                    </div>

                    <!-- Course 3 -->
                    <div class="glass-card p-6 flex flex-col group relative overflow-hidden">
                        <div class="h-48 rounded-xl mb-6 relative overflow-hidden group-hover:scale-105 transition duration-500">
                            <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?q=80&w=800&auto=format&fit=crop" class="w-full h-full object-cover opacity-70">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent flex items-end p-4">
                                <span class="bg-[#7000ff]/80 backdrop-blur text-white text-xs px-2 py-1 rounded"><i class="fas fa-video me-1"></i> 30+ Hours Video</span>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">React.js & Node.js Bootcamp</h3>
                        <p class="text-gray-400 text-sm mb-6 flex-grow line-clamp-3">Become a modern MERN stack developer. Learn React components, state management, Node APIs, and MongoDB.</p>
                        
                        <div class="flex justify-between items-center pt-4 border-t border-white/10 mt-auto">
                            <div>
                                <div class="text-gray-500 text-xs line-through">₹5,999</div>
                                <div class="text-2xl font-bold text-[#00f2ff]">₹1,299</div>
                            </div>
                            <a href="buy_course.php?course_id=3" class="btn-primary px-6 py-2 rounded-lg font-bold text-sm text-white shadow-[0_0_15px_rgba(0,242,255,0.3)]">Buy Now</a>
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>
        <!-- ================= END COURSES SECTION ================= -->

        <!-- Contact Section -->
        <section id="contact" class="py-24 relative z-10 px-6">
            <div class="max-w-4xl mx-auto glass-panel p-8 md:p-12 rounded-3xl border border-[#00f2ff]/20 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-[#7000ff] rounded-full blur-[100px] opacity-30 -translate-y-1/2 translate-x-1/2"></div>
                
                <div class="relative z-10 text-center mb-10">
                    <h2 class="text-3xl font-bold mb-2">Connect With <span class="text-gradient">Us</span></h2>
                    <p class="text-gray-400">Join our CRM system to become a volunteer or partner.</p>
                </div>
                
                <?php if($success_msg): ?>
                    <div class="relative z-10 bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded mb-6 text-center">
                        <i class="fas fa-check-circle me-2"></i> <?= $success_msg ?>
                    </div>
                <?php endif; ?>
                <?php if($error_msg): ?>
                    <div class="relative z-10 bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded mb-6 text-center">
                        <i class="fas fa-exclamation-circle me-2"></i> <?= $error_msg ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="#contact" class="relative z-10 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2 font-medium">Full Name *</label>
                            <input type="text" name="name" required placeholder="John Doe" class="glass-input">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2 font-medium">Email Address *</label>
                            <input type="email" name="email" required placeholder="john@example.com" class="glass-input">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2 font-medium">Phone Number *</label>
                            <input type="tel" name="phone" required placeholder="+91 XXXXX XXXXX" class="glass-input">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2 font-medium">Interest Area</label>
                        <select name="interest" class="glass-input appearance-none">
                            <option value="volunteer" class="text-gray-800">Become a Volunteer</option>
                            <option value="partner" class="text-gray-800">Corporate Partnership</option>
                            <option value="donate" class="text-gray-800">Make a Donation</option>
                            <option value="courses" class="text-gray-800">Course Inquiry</option>
                            <option value="other" class="text-gray-800">Other Inquiries</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-2 font-medium">Message (Optional)</label>
                        <textarea name="message" rows="3" placeholder="How would you like to help?" class="glass-input resize-none"></textarea>
                    </div>
                    <button type="submit" name="submit_contact" class="w-full btn-primary text-white font-bold py-4 rounded-xl uppercase tracking-widest text-sm shadow-[0_0_20px_rgba(112,0,255,0.4)]">
                        Submit Request
                    </button>
                </form>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 border-t border-white/10 relative z-10 bg-black/60 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 text-white">Dynamic Foundation <span class="text-[#00f2ff]">Trust</span></h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-4">Empowering communities through innovative solutions, compassion, and digital education.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-[#00f2ff] hover:bg-white/10 transition"><i class="fab fa-facebook-f text-sm"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-[#00f2ff] hover:bg-white/10 transition"><i class="fab fa-twitter text-sm"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-[#00f2ff] hover:bg-white/10 transition"><i class="fab fa-instagram text-sm"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-[#00f2ff] hover:bg-white/10 transition"><i class="fab fa-linkedin-in text-sm"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4 uppercase tracking-widest text-sm">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#about" class="hover:text-[#00f2ff] transition">About Us</a></li>
                        <li><a href="#programs" class="hover:text-[#00f2ff] transition">Our NGO Programs</a></li>
                        <li><a href="#courses" class="hover:text-[#00f2ff] transition">Skill Courses</a></li>
                        <li><a href="user/login.php" class="hover:text-[#00f2ff] transition">Student/Donor Login</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4 uppercase tracking-widest text-sm">Contact Info</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-start gap-3"><i class="fas fa-map-marker-alt text-[#00f2ff] mt-1"></i> Deoria, Uttar Pradesh, India</li>
                        <li class="flex items-center gap-3"><i class="fas fa-envelope text-[#7000ff]"></i> socialtradingcsc@gmail.com</li>
                        <li class="flex items-center gap-3"><i class="fas fa-phone text-[#00f2ff]"></i> +91-9598162006</li>
                    </ul>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-6 pt-6 border-t border-white/10 text-center flex flex-col md:flex-row justify-between items-center text-xs text-gray-500">
                <p>&copy; 2026 Dynamic Foundation Trust. All rights reserved.</p>
                <div class="mt-4 md:mt-0 space-x-4">
                    <a href="admin/login.php" class="hover:text-gray-300">Admin Login</a>
                </div>
            </div>
        </footer>
    </div>

    <script>
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(60, window.innerWidth/window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        renderer.setSize(window.innerWidth, window.innerHeight); 
        document.getElementById('canvas-container').appendChild(renderer.domElement);

        const particles = new THREE.BufferGeometry();
        const pos = new Float32Array(4500);
        for(let i=0; i<4500; i++) {
            pos[i] = (Math.random()-0.5)*60;
        }
        particles.setAttribute('position', new THREE.BufferAttribute(pos, 3));
        
        const mesh = new THREE.Points(particles, new THREE.PointsMaterial({ 
            size: 0.16, 
            color: 0x00f2ff, 
            transparent: true, 
            blending: THREE.AdditiveBlending 
        }));
        scene.add(mesh);
        
        const sphere = new THREE.Mesh(
            new THREE.SphereGeometry(8, 16, 16), 
            new THREE.MeshBasicMaterial({ 
                color: 0x7000ff, 
                wireframe: true, 
                transparent: true, 
                opacity: 0.4 
            })
        );
        scene.add(sphere); 
        camera.position.z = 25;

        let mX = 0, mY = 0, sY = 0;
        document.addEventListener('mousemove', e => { 
            mX = e.clientX - window.innerWidth/2; 
            mY = e.clientY - window.innerHeight/2; 
        });
        
        window.addEventListener('scroll', () => {
            sY = window.scrollY;
        });
        
        function animate() { 
            requestAnimationFrame(animate); 
            mesh.rotation.y += 0.001 + 0.05*(mX*0.001 - mesh.rotation.y); 
            mesh.rotation.x += 0.0005 + 0.05*(mY*0.001 - mesh.rotation.x); 
            
            camera.position.y = -sY * 0.005; 
            
            sphere.rotation.y -= 0.002; 
            sphere.rotation.x += 0.001; 
            renderer.render(scene, camera); 
        }
        
        window.addEventListener('resize', () => { 
            camera.aspect = window.innerWidth/window.innerHeight; 
            camera.updateProjectionMatrix(); 
            renderer.setSize(window.innerWidth, window.innerHeight); 
        });
        
        animate();

        document.addEventListener('DOMContentLoaded', () => {
            const counters = document.querySelectorAll('.counter');
            let counted = false;

            const runCounters = () => {
                counters.forEach(counter => {
                    const target = +counter.getAttribute('data-target');
                    const duration = 2000;
                    const increment = target / (duration / 16);
                    let current = 0;

                    const updateCounter = () => {
                        current += increment;
                        if (current < target) {
                            counter.innerText = Math.ceil(current).toLocaleString();
                            requestAnimationFrame(updateCounter);
                        } else {
                            counter.innerText = target.toLocaleString();
                        }
                    };
                    updateCounter();
                });
            };

            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && !counted) {
                    runCounters();
                    counted = true;
                }
            }, { threshold: 0.5 });
            
            const impactSection = document.getElementById('impact');
            if(impactSection) observer.observe(impactSection);
        });
    </script>
</body>
</html>