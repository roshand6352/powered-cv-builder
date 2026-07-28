<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
    --navy:#172d40;
    --navy-dark:#0f1e2b;
    --navy-light:#1e3a52;
    --orange:#c1623d;
    --orange-light:#d4744f;
    --orange-pale:#fdf0eb;
    --orange-mid:#f5ddd3;
    --cream:#faf8f5;
    --white:#ffffff;
    --text-body:#3a4a58;
    --text-muted:#7a8a98;
    --border:#e4e8ec;
    --radius-sm:8px;
    --radius-md:12px;
    --radius-lg:20px;
    --radius-xl:32px;
}
html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;color:var(--text-body);background:var(--white);line-height:1.6;overflow-x:hidden}
h1,h2,h3,h4{font-family:'Sora',sans-serif;color:var(--navy);line-height:1.2}
.container{max-width:1140px;margin:0 auto;padding:0 24px}
img{max-width:100%;height:auto;display:block}

nav{position:sticky;top:0;z-index:100;background:rgba(255,255,255,0.96);backdrop-filter:blur(12px);border-bottom:1px solid var(--border)}
.nav-inner{display:flex;align-items:center;justify-content:space-between;height:68px;gap:16px}
.nav-logo img{height:36px;width:auto;display:block;object-fit:contain}
.site-logo{display:inline-block}
.site-logo img{height:36px;width:auto;max-width:160px;display:block;object-fit:contain}
.nav-links{display:flex;align-items:center;gap:32px;list-style:none}
.nav-links a{text-decoration:none;color:var(--navy);font-size:15px;font-weight:500;transition:color .2s}
.nav-links a:hover{color:var(--orange)}
.nav-actions{display:flex;align-items:center;gap:12px}
.nav-toggle{display:none;background:none;border:none;cursor:pointer;padding:8px;color:var(--navy)}
.nav-toggle svg{width:24px;height:24px}
.nav-mobile{display:none;position:fixed;inset:68px 0 0 0;background:rgba(255,255,255,0.98);backdrop-filter:blur(12px);padding:24px;z-index:99;border-top:1px solid var(--border);overflow-y:auto}
.nav-mobile.open{display:block}
.nav-mobile-links{list-style:none;display:flex;flex-direction:column;gap:4px;margin-bottom:20px}
.nav-mobile-links a{display:block;padding:14px 0;text-decoration:none;color:var(--navy);font-size:16px;font-weight:600;border-bottom:1px solid var(--border)}
.nav-mobile-actions{display:flex;flex-direction:column;gap:10px}

.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;font-family:'DM Sans',sans-serif;font-weight:600;font-size:14px;border-radius:var(--radius-sm);padding:10px 20px;text-decoration:none;cursor:pointer;transition:all .2s;border:none;white-space:nowrap}
.btn-ghost{background:transparent;color:var(--navy);border:1.5px solid var(--border)}
.btn-ghost:hover{border-color:var(--navy);background:var(--cream)}
.btn-primary{background:var(--orange);color:white}
.btn-primary:hover{background:var(--orange-light);transform:translateY(-1px)}
.btn-outline{background:transparent;color:var(--navy);border:2px solid var(--navy)}
.btn-outline:hover{background:var(--navy);color:white}
.btn-primary-lg{font-size:16px;padding:14px 28px;border-radius:var(--radius-md)}
.btn-outline-lg{font-size:16px;padding:14px 28px;border-radius:var(--radius-md);background:transparent;color:var(--navy);border:2px solid var(--navy)}
.btn-outline-lg:hover{background:var(--navy);color:white}
.btn-white{background:white;color:var(--navy)}
.btn-white:hover{background:var(--cream)}
.btn-white-outline{background:transparent;color:white;border:2px solid rgba(255,255,255,0.5)}
.btn-white-outline:hover{background:rgba(255,255,255,0.1);border-color:white}

.hero{background:var(--navy);min-height:92vh;display:flex;align-items:center;position:relative;overflow:hidden;padding:80px 0}
.hero::before{content:'';position:absolute;top:-120px;right:-120px;width:600px;height:600px;background:radial-gradient(circle,rgba(193,98,61,0.18) 0%,transparent 70%);pointer-events:none}
.hero::after{content:'';position:absolute;bottom:-80px;left:-80px;width:400px;height:400px;background:radial-gradient(circle,rgba(193,98,61,0.1) 0%,transparent 70%);pointer-events:none}
.hero-grid{display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;position:relative;z-index:1}
.hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(193,98,61,0.15);border:1px solid rgba(193,98,61,0.3);color:#f0a07a;font-size:12px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;padding:6px 14px;border-radius:40px;margin-bottom:24px}
.hero h1{font-size:clamp(36px,4.5vw,58px);font-weight:800;color:white;line-height:1.1;margin-bottom:22px}
.hero h1 span{color:var(--orange)}
.hero-sub{font-size:17px;color:rgba(255,255,255,0.7);line-height:1.7;margin-bottom:36px;max-width:480px}
.hero-ctas{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:36px}
.hero-trust{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.stars{color:#f5a623;font-size:16px;letter-spacing:1px}
.hero-trust-text{font-size:13px;color:rgba(255,255,255,0.55)}
.hero-trust-text b{color:rgba(255,255,255,0.8);font-weight:500}

.hero-visual{position:relative}
.cv-preview-wrap{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:var(--radius-xl);padding:24px;backdrop-filter:blur(4px)}
.cv-mock{background:white;border-radius:var(--radius-md);overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,0.4)}
.cv-mock-header{background:var(--navy);padding:20px 24px;display:flex;align-items:center;gap:14px}
.cv-avatar{width:44px;height:44px;border-radius:50%;background:var(--orange);display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-weight:700;font-size:16px;color:white;flex-shrink:0}
.cv-mock-name{color:white;font-family:'Sora',sans-serif;font-size:15px;font-weight:700}
.cv-mock-title{color:rgba(255,255,255,0.6);font-size:12px;margin-top:2px}
.cv-mock-body{padding:20px 24px}
.cv-section-label{font-size:10px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--orange);margin-bottom:8px;margin-top:16px}
.cv-section-label:first-child{margin-top:0}
.cv-bar-row{display:flex;align-items:center;gap:10px;margin-bottom:6px}
.cv-bar-label{font-size:11px;color:var(--text-body);width:80px;flex-shrink:0}
.cv-bar-track{flex:1;height:6px;background:#f0f0f0;border-radius:3px;overflow:hidden}
.cv-bar-fill{height:100%;border-radius:3px;background:var(--navy)}
.cv-line{height:7px;background:#f0f0f0;border-radius:4px;margin-bottom:6px}
.cv-line.short{width:60%}
.cv-line.med{width:80%}
.cv-line.full{width:100%}
.cv-line.narrow{width:40%}
.cv-ai-badge{display:inline-flex;align-items:center;gap:6px;background:var(--orange-pale);border:1px solid var(--orange-mid);color:var(--orange);font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;margin-top:10px}
.step-pills{display:flex;flex-direction:column;gap:10px;margin-top:16px}
.step-pill{background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);border-radius:10px;padding:10px 14px;display:flex;align-items:center;gap:12px}
.step-num{width:26px;height:26px;background:var(--orange);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:white;flex-shrink:0}
.step-pill-text{font-size:13px;color:rgba(255,255,255,0.75)}

.stats-bar{background:var(--orange);padding:22px 0}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:rgba(255,255,255,0.2)}
.stat-item{background:var(--orange);padding:18px 24px;text-align:center}
.stat-num{font-family:'Sora',sans-serif;font-size:clamp(20px,3vw,26px);font-weight:800;color:white;line-height:1}
.stat-label{font-size:13px;color:rgba(255,255,255,0.8);margin-top:4px}

.pain{padding:100px 0;background:var(--cream)}
.section-tag{display:inline-block;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--orange);margin-bottom:14px}
.section-title{font-size:clamp(28px,3.5vw,44px);font-weight:800;margin-bottom:18px}
.section-sub{font-size:17px;color:var(--text-muted);max-width:560px;line-height:1.7}
.pain-intro{margin-bottom:60px}
.pain-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.pain-card{background:white;border-radius:var(--radius-lg);padding:36px 32px;border:1px solid var(--border);position:relative;overflow:hidden;transition:transform .25s,box-shadow .25s}
.pain-card:hover{transform:translateY(-4px);box-shadow:0 16px 48px rgba(23,45,64,0.1)}
.pain-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--orange)}
.pain-icon{width:52px;height:52px;background:var(--orange-pale);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;margin-bottom:20px}
.pain-icon svg{width:26px;height:26px;stroke:var(--orange);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.pain-card h3{font-size:18px;font-weight:700;margin-bottom:12px;color:var(--navy)}
.pain-card p{font-size:15px;color:var(--text-muted);line-height:1.7}

.tools{padding:100px 0;background:white}
.tools-title{text-align:center;margin-bottom:16px}
.tools-sub{text-align:center;font-size:17px;color:var(--text-muted);margin-bottom:60px;max-width:640px;margin-left:auto;margin-right:auto}
.tools-grid{display:grid;grid-template-columns:1fr 1fr;gap:28px;align-items:start}
.tool-card{border:2px solid var(--border);border-radius:var(--radius-xl);padding:40px 36px;position:relative;transition:border-color .25s}
.tool-card.featured{border-color:var(--orange)}
.tool-start-badge{position:absolute;top:-16px;left:50%;transform:translateX(-50%);background:var(--orange);color:white;font-size:12px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;padding:6px 18px;border-radius:20px;white-space:nowrap}
.tool-icon-wrap{width:60px;height:60px;background:var(--navy);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;margin-bottom:22px}
.tool-icon-wrap svg{width:30px;height:30px;stroke:white;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.tool-card h3{font-size:22px;font-weight:700;margin-bottom:12px}
.tool-card>p{font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:24px}
.feature-list{list-style:none;display:flex;flex-direction:column;gap:10px}
.feature-list li{display:flex;align-items:flex-start;gap:10px;font-size:14px;line-height:1.5}
.feat-free{color:var(--navy)}
.feat-pro{color:var(--orange)}
.feat-icon{flex-shrink:0;font-size:15px;margin-top:1px}
.tool-cta{margin-top:28px}

.how{padding:100px 0;background:var(--cream)}
.how-tool-label{display:inline-flex;align-items:center;gap:8px;background:var(--navy);color:white;font-size:12px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;padding:8px 16px;border-radius:30px;margin-bottom:20px}
.how-heading{font-size:clamp(24px,3vw,36px);font-weight:800;margin-bottom:8px}
.how-sub{font-size:15px;color:var(--text-muted);margin-bottom:48px}
.steps-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:32px}
.how-block{margin-bottom:72px}
.how-block:last-child{margin-bottom:0}
.how-cta{text-align:center}
.step-card{background:white;border-radius:var(--radius-lg);padding:28px 24px;border:1px solid var(--border);position:relative}
.step-num-big{font-family:'Sora',sans-serif;font-size:42px;font-weight:800;color:var(--orange-mid);line-height:1;margin-bottom:14px}
.step-card h4{font-size:15px;font-weight:700;margin-bottom:8px}
.step-card p{font-size:13px;color:var(--text-muted);line-height:1.6}

.why{padding:100px 0;background:var(--navy);position:relative;overflow:hidden}
.why::before{content:'';position:absolute;top:0;right:0;width:500px;height:500px;background:radial-gradient(circle,rgba(193,98,61,0.12) 0%,transparent 70%);pointer-events:none}
.why .section-title{color:white}
.why-tagline{font-size:17px;color:rgba(255,255,255,0.55);margin-bottom:60px;max-width:500px}
.why-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:24px;position:relative;z-index:1}
.why-card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:var(--radius-lg);padding:32px 28px;transition:background .25s}
.why-card:hover{background:rgba(255,255,255,0.08)}
.why-icon{width:48px;height:48px;background:rgba(193,98,61,0.2);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;margin-bottom:18px}
.why-icon svg{width:24px;height:24px;stroke:var(--orange);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.why-card h3{font-size:17px;font-weight:700;color:white;margin-bottom:8px}
.why-card p{font-size:14px;color:rgba(255,255,255,0.55);line-height:1.7}

.pricing{padding:100px 0;background:white}
.pricing-title{text-align:center;margin-bottom:10px}
.pricing-sub{text-align:center;font-size:16px;color:var(--text-muted);margin-bottom:60px}
.pricing-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:28px;margin:0 auto}
.price-card{border:2px solid var(--border);border-radius:var(--radius-xl);padding:40px 36px}
.price-card.pro{border-color:var(--plan-badge-color,var(--orange));position:relative}
.pro-badge{position:absolute;top:-14px;left:50%;transform:translateX(-50%);background:var(--plan-badge-color,var(--orange));color:white;font-size:11px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;padding:5px 16px;border-radius:20px;white-space:nowrap}
.price-tier{font-size:13px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--plan-title-color,var(--text-muted));margin-bottom:10px}
.price-amount{font-family:'Sora',sans-serif;font-size:clamp(36px,5vw,48px);font-weight:800;color:var(--plan-price-color,var(--navy));line-height:1}
.price-amount span{font-size:18px;color:var(--text-muted);font-weight:400}
.price-desc{font-size:14px;color:var(--text-muted);margin-bottom:28px;margin-top:6px}
.price-features{list-style:none;display:flex;flex-direction:column;gap:12px;margin-bottom:32px}
.price-features li{display:flex;align-items:flex-start;gap:10px;font-size:14px;color:var(--plan-feature-color,var(--text-body))}
.price-features li.na{color:var(--text-muted);opacity:0.6}
.pf-check{color:var(--navy);font-weight:600;flex-shrink:0}
.pf-check.orange{color:var(--orange)}
.pf-cross{color:var(--text-muted);flex-shrink:0}
.watermark-note{font-size:12px;color:var(--text-muted);margin-top:16px;padding:12px;background:var(--cream);border-radius:var(--radius-sm)}
.payment-note{font-size:13px;color:var(--text-muted);margin-top:14px;text-align:center}

.testimonials{padding:100px 0;background:var(--cream)}
.testi-title{text-align:center;margin-bottom:56px}
.testi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.testi-card{background:white;border-radius:var(--radius-lg);padding:32px 28px;border:1px solid var(--border)}
.testi-stars{color:#f5a623;font-size:17px;letter-spacing:2px;margin-bottom:16px}
.testi-quote{font-size:15px;color:var(--text-body);line-height:1.7;margin-bottom:22px;font-style:italic}
.testi-name{font-weight:700;font-size:14px;color:var(--navy)}
.testi-meta{font-size:13px;color:var(--text-muted);margin-top:2px}

.faq{padding:100px 0;background:white}
.faq-title{text-align:center;margin-bottom:56px}
.faq-list{max-width:720px;margin:0 auto;display:flex;flex-direction:column;gap:0}
.faq-item{border-bottom:1px solid var(--border)}
.faq-q{width:100%;background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:space-between;padding:22px 0;text-align:left;font-family:'DM Sans',sans-serif;font-size:16px;font-weight:600;color:var(--navy);transition:color .2s}
.faq-q:hover{color:var(--orange)}
.faq-arrow{transition:transform .3s;color:var(--text-muted);font-size:20px;flex-shrink:0;margin-left:12px}
.faq-item.open .faq-arrow{transform:rotate(180deg)}
.faq-a{max-height:0;overflow:hidden;transition:max-height .35s ease}
.faq-item.open .faq-a{max-height:400px}
.faq-a-inner{padding-bottom:20px;font-size:15px;color:var(--text-muted);line-height:1.75}

.cta-section{padding:100px 0;background:var(--navy);text-align:center;position:relative;overflow:hidden}
.cta-section::before{content:'';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:700px;height:700px;background:radial-gradient(circle,rgba(193,98,61,0.15) 0%,transparent 60%);pointer-events:none}
.cta-section h2{font-size:clamp(28px,4vw,50px);font-weight:800;color:white;margin-bottom:18px;position:relative}
.cta-section p{font-size:17px;color:rgba(255,255,255,0.6);max-width:560px;margin:0 auto 40px;line-height:1.7;position:relative}
.cta-btns{display:flex;gap:16px;justify-content:center;flex-wrap:wrap;position:relative}

footer{background:var(--navy-dark);padding:64px 0 32px}
.footer-top{display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:48px;margin-bottom:48px}
.footer-logo{height:34px;margin-bottom:16px}
.footer-tagline{font-size:14px;color:rgba(255,255,255,0.45);line-height:1.7;max-width:220px}
.footer-col h4{font-size:13px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;color:rgba(255,255,255,0.4);margin-bottom:16px}
.footer-col ul{list-style:none;display:flex;flex-direction:column;gap:10px}
.footer-col a{font-size:14px;color:rgba(255,255,255,0.55);text-decoration:none;transition:color .2s}
.footer-col a:hover{color:var(--orange)}
.footer-bottom{border-top:1px solid rgba(255,255,255,0.08);padding-top:28px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.footer-copy{font-size:13px;color:rgba(255,255,255,0.35)}
.footer-badges{display:flex;gap:10px;flex-wrap:wrap}
.f-badge{font-size:11px;font-weight:600;padding:5px 12px;border-radius:20px;border:1px solid rgba(255,255,255,0.15);color:rgba(255,255,255,0.45);letter-spacing:0.04em}

@media(max-width:900px){
    .nav-links,.nav-actions{display:none}
    .nav-toggle{display:block}
    .hero-grid,.tools-grid,.footer-top{grid-template-columns:1fr}
    .pricing-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
    .hero{min-height:auto;padding:64px 0}
    .hero-visual{display:none}
    .pain-cards,.steps-grid,.testi-grid{grid-template-columns:1fr}
    .stats-grid{grid-template-columns:repeat(2,1fr)}
    .why-grid{grid-template-columns:1fr}
    .pain,.tools,.how,.why,.pricing,.testimonials,.faq,.cta-section{padding:72px 0}
    .tool-card,.price-card{padding:32px 24px}
}
@media(max-width:480px){
    .hero-ctas,.cta-btns{flex-direction:column;width:100%}
    .hero-ctas .btn,.cta-btns .btn{width:100%}
    .stats-grid{grid-template-columns:1fr}
    .contact-grid,.plan-grid,.pricing-grid{grid-template-columns:1fr}
}

/* Inner pages */
.page-hero{padding:64px 0 56px;background:var(--cream);border-bottom:1px solid var(--border);text-align:center}
.page-hero h1{font-size:clamp(32px,4vw,44px);font-weight:800;margin-bottom:14px;color:var(--navy)}
.page-hero p{font-size:17px;color:var(--text-muted);max-width:560px;margin:0 auto;line-height:1.7}

.page-section{padding:80px 0}
.page-section--white{background:white}
.page-section--cream{background:var(--cream)}

.section-intro{text-align:center;margin-bottom:48px}
.section-intro .section-tag{display:block;text-align:center;margin-bottom:14px}
.section-intro h2{font-size:clamp(28px,3.5vw,36px);font-weight:800;margin-bottom:12px}
.section-intro p{font-size:17px;color:var(--text-muted);margin:0 auto;line-height:1.7}

.prose-content{max-width:720px;margin:0 auto}
.prose-content h2{font-family:'Sora',sans-serif;font-size:22px;font-weight:700;margin:36px 0 14px;color:var(--navy)}
.prose-content h2:first-child{margin-top:0}
.prose-content p,.prose-content li{font-size:15px;color:var(--text-muted);line-height:1.75}
.prose-content p{margin-bottom:14px}
.prose-content ul{margin:14px 0 20px;padding-left:20px}
.prose-content a{color:var(--orange);font-weight:600;text-decoration:none}
.prose-content a:hover{color:var(--orange-light)}
.prose-content strong{color:var(--navy)}

.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:start}
.contact-info h2{font-size:24px;font-weight:700;margin-bottom:14px;color:var(--navy)}
.contact-info>p{font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:28px}
.contact-dl{display:flex;flex-direction:column;gap:20px}
.contact-dl dt{font-size:13px;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:0.04em}
.contact-dl dd{margin-top:4px;font-size:15px;color:var(--text-muted)}
.contact-card{background:var(--cream);border:1px solid var(--border);border-radius:var(--radius-lg);padding:32px 28px}

.form-group{margin-bottom:18px}
.form-label{display:block;font-size:14px;font-weight:600;color:var(--navy);margin-bottom:6px}
.form-input,.form-select,.form-textarea{width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-family:'DM Sans',sans-serif;font-size:14px;color:var(--text-body);background:white;transition:border-color .2s,box-shadow .2s}
.form-input:focus,.form-select:focus,.form-textarea:focus{outline:none;border-color:var(--orange);box-shadow:0 0 0 3px rgba(193,98,61,0.12)}
.form-textarea{resize:vertical;min-height:120px}
.form-note{font-size:12px;color:var(--text-muted);text-align:center;margin-top:14px}
.form-error{font-size:13px;color:#b03a2e;margin-top:6px;list-style:none;padding:0}
.form-error li+li{margin-top:4px}
.form-status{font-size:13px;color:#1e7e4a;margin-bottom:16px;padding:12px 14px;background:var(--orange-pale);border:1px solid var(--orange-mid);border-radius:var(--radius-sm)}
.form-row-between{display:flex;flex-direction:column;gap:14px;margin-top:22px}
.checkbox-row{display:flex;align-items:center;gap:8px;font-size:14px;color:var(--text-muted);cursor:pointer}
.checkbox-row input{width:16px;height:16px;accent-color:var(--orange)}
.link-orange{color:var(--orange);font-weight:600;text-decoration:none}
.link-orange:hover{color:var(--orange-light)}
.btn-block{width:100%;justify-content:center}
.btn-sm{font-size:13px;padding:8px 16px}

.plan-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px}
.plan-card{background:white;border:2px solid var(--border);border-radius:var(--radius-xl);padding:36px 32px;display:flex;flex-direction:column;position:relative}
.plan-card--featured{border-color:var(--plan-accent,var(--orange));box-shadow:0 16px 48px rgba(193,98,61,0.12)}
.plan-badge{position:absolute;top:-14px;left:50%;transform:translateX(-50%);background:var(--plan-accent,var(--orange));color:white;font-size:11px;font-weight:700;padding:5px 16px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap}
.plan-card h3{font-size:20px;font-weight:700;margin-bottom:6px;color:var(--navy)}
.plan-card .plan-desc{font-size:14px;color:var(--text-muted);margin-bottom:20px;line-height:1.6}
.plan-price{font-family:'Sora',sans-serif;font-size:40px;font-weight:800;color:var(--navy);line-height:1;margin-bottom:4px}
.plan-price span{font-size:15px;font-weight:400;color:var(--text-muted)}
.plan-features{list-style:none;margin:24px 0;display:flex;flex-direction:column;gap:10px;flex:1}
.plan-features li{display:flex;align-items:flex-start;gap:10px;font-size:14px;color:var(--text-body);line-height:1.5}
.plan-features .pf-check{color:var(--plan-accent,var(--orange));font-weight:700;flex-shrink:0}
.plan-features li.na{color:var(--text-muted)}
.plan-features .pf-cross{color:var(--text-muted);font-weight:700;flex-shrink:0}
.plan-footer-note{margin-top:16px;font-size:12px;color:var(--text-muted);line-height:1.5;text-align:center}

.template-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px}
.template-card{border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;background:white;display:flex;flex-direction:column;transition:transform .25s,box-shadow .25s}
.template-card:hover{transform:translateY(-4px);box-shadow:0 16px 48px rgba(23,45,64,0.1)}
.template-preview{aspect-ratio:3/4;background:var(--cream);overflow:hidden}
.template-preview img{width:100%;height:100%;object-fit:cover;object-position:top}
.template-placeholder{height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--orange-pale),var(--orange-mid));padding:24px}
.template-placeholder-inner{width:100%;background:rgba(255,255,255,0.92);border-radius:var(--radius-sm);padding:16px}
.template-body{padding:22px 24px;display:flex;flex-direction:column;flex:1}
.template-body h3{font-size:18px;font-weight:700;color:var(--navy);margin-bottom:8px}
.template-body p{font-size:14px;color:var(--text-muted);line-height:1.6;flex:1;margin-bottom:20px}
.empty-state{border:2px dashed var(--border);border-radius:var(--radius-lg);background:white;padding:48px 24px;text-align:center}
.empty-state h2{font-size:18px;font-weight:700;color:var(--navy);margin-bottom:8px}
.empty-state p{font-size:14px;color:var(--text-muted)}

.data-table-wrap{overflow-x:auto;border-radius:var(--radius-lg);border:1px solid var(--border)}
.data-table{width:100%;min-width:640px;border-collapse:collapse;font-size:14px}
.data-table th,.data-table td{padding:16px 24px;text-align:left;border-bottom:1px solid var(--border)}
.data-table th{background:var(--cream);font-weight:700;color:var(--navy);font-family:'Sora',sans-serif}
.data-table td{color:var(--text-body)}
.data-table tr:last-child td,.data-table tr:last-child th{border-bottom:none}
.data-table .col-accent{color:var(--orange);font-weight:600}

.inner-cta{background:var(--navy);padding:80px 0;text-align:center;position:relative;overflow:hidden}
.inner-cta::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at center,rgba(193,98,61,0.15) 0%,transparent 60%);pointer-events:none}
.inner-cta h2{color:white;font-size:clamp(28px,3vw,36px);font-weight:800;margin-bottom:14px;position:relative}
.inner-cta p{color:rgba(255,255,255,0.6);max-width:560px;margin:0 auto 32px;font-size:17px;line-height:1.7;position:relative}
.inner-cta-btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;position:relative}

.faq-box{max-width:720px;margin:0 auto;border:1px solid var(--border);border-radius:var(--radius-lg);background:white;padding:0 28px}

/* Auth pages */
.auth-wrap{min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 24px;background:linear-gradient(160deg,var(--cream) 0%,#fff 45%,var(--orange-pale) 100%)}
.auth-logo{display:block;margin-bottom:32px;text-align:center}
.auth-logo img{height:48px;width:auto;max-width:220px;margin:0 auto;object-fit:contain}
.auth-card{width:100%;max-width:420px;background:white;border:1px solid var(--border);border-radius:var(--radius-lg);padding:36px 32px;box-shadow:0 16px 48px rgba(23,45,64,0.08)}
.auth-card h1{font-family:'Sora',sans-serif;font-size:24px;font-weight:800;text-align:center;margin-bottom:8px;color:var(--navy)}
.auth-sub{text-align:center;font-size:14px;color:var(--text-muted);margin-bottom:24px;line-height:1.6}
.auth-back{margin-top:24px;text-align:center;font-size:14px;color:var(--text-muted)}
.auth-back a{color:var(--orange);font-weight:600;text-decoration:none}
.auth-back a:hover{color:var(--orange-light)}
.auth-links{margin-top:20px;text-align:center;font-size:14px;color:var(--text-muted)}
.auth-links a{color:var(--orange);font-weight:600;text-decoration:none}
.auth-divider{display:flex;align-items:center;gap:12px;margin:0 0 20px;color:var(--text-muted);font-size:13px;text-transform:lowercase}
.auth-divider::before,.auth-divider::after{content:'';flex:1;height:1px;background:var(--border)}
.btn-google{background:white;color:var(--navy);border:1.5px solid var(--border);font-size:14px;padding:11px 20px;margin-bottom:20px}
.btn-google:hover{background:var(--cream);border-color:#c8d0d8;transform:none}
.btn-google-icon{width:18px;height:18px;flex-shrink:0}

@media(min-width:640px){
    .form-row-between{flex-direction:row;align-items:center;justify-content:space-between}
}
@media(max-width:900px){
    .page-section{padding:64px 0}
    .contact-grid{grid-template-columns:1fr}
    .inner-cta{padding:64px 0}
}

/* Accessibility */
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}

/* Blog listing */
.blog-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:28px}
.blog-card{background:white;border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;box-shadow:0 8px 24px rgba(23,45,64,0.06);transition:transform .25s ease,box-shadow .25s ease}
.blog-card:hover{transform:translateY(-6px);box-shadow:0 18px 48px rgba(23,45,64,0.12)}
.blog-card-link{display:flex;flex-direction:column;height:100%;text-decoration:none;color:inherit}
.blog-card-link:focus-visible{outline:3px solid rgba(193,98,61,0.45);outline-offset:3px}
.blog-card-media{aspect-ratio:16/9;overflow:hidden;background:var(--cream)}
.blog-card-media img{width:100%;height:100%;object-fit:cover;transition:transform .35s ease}
.blog-card:hover .blog-card-media img{transform:scale(1.06)}
.blog-card-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--navy),var(--navy-light));color:white;font-family:'Sora',sans-serif;font-size:42px;font-weight:800}
.blog-card-body{padding:24px 22px 26px;display:flex;flex-direction:column;flex:1}
.blog-card-meta{display:flex;flex-wrap:wrap;align-items:center;gap:8px;font-size:13px;color:var(--text-muted);margin-bottom:12px}
.blog-card-title{font-size:20px;font-weight:700;color:var(--navy);margin-bottom:10px;line-height:1.35}
.blog-card-excerpt{font-size:14px;color:var(--text-muted);line-height:1.7;margin-bottom:18px;flex:1}
.blog-card-cta{display:inline-flex;align-items:center;gap:8px;margin-top:auto;font-size:14px;font-weight:700;color:var(--orange)}
.blog-card-cta svg{width:16px;height:16px;transition:transform .2s ease}
.blog-card:hover .blog-card-cta svg{transform:translateX(3px)}
.blog-empty{padding:64px 24px}
.blog-empty-icon{width:64px;height:64px;margin:0 auto 18px;border-radius:16px;background:var(--orange-pale);color:var(--orange);display:flex;align-items:center;justify-content:center}
.blog-empty-icon svg{width:30px;height:30px}
.blog-pagination{margin-top:48px;display:flex;justify-content:center}
.blog-pagination-inner{display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap}
.blog-page-numbers{display:flex;align-items:center;gap:6px;list-style:none;margin:0;padding:0}
.blog-page-btn,.blog-page-num{display:inline-flex;align-items:center;justify-content:center;min-height:40px;padding:0 14px;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:white;color:var(--navy);font-size:14px;font-weight:600;text-decoration:none;transition:all .2s}
.blog-page-btn:hover,.blog-page-num:hover{border-color:var(--orange);color:var(--orange)}
.blog-page-num.is-current{background:var(--navy);border-color:var(--navy);color:white}
.blog-page-btn.is-disabled{opacity:0.45;pointer-events:none}
.blog-page-ellipsis{display:inline-flex;align-items:center;justify-content:center;min-width:28px;color:var(--text-muted)}
.blog-article{max-width:820px}
.blog-article-header{margin-bottom:28px}
.blog-article-meta{justify-content:center;margin-bottom:0}
.blog-article-image{margin:0 0 36px;border-radius:var(--radius-lg);overflow:hidden;border:1px solid var(--border)}
.blog-article-image img{width:100%;height:auto;display:block}
.blog-article-content{max-width:none}
.blog-article-content img{max-width:100%;height:auto;border-radius:var(--radius-sm);margin:18px 0}
.blog-article-content h2,.blog-article-content h3{margin-top:28px}
.blog-article-content blockquote{border-left:3px solid var(--orange);margin:24px 0;padding:12px 18px;background:var(--cream);color:var(--text-body);font-style:italic}
.blog-article-content pre,.blog-article-content code{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace}
.blog-article-content pre{background:var(--navy);color:#f5f7fa;padding:16px 18px;border-radius:var(--radius-sm);overflow-x:auto;margin:20px 0;font-size:13px;line-height:1.6}
.blog-article-content code{background:var(--cream);color:var(--navy);padding:2px 6px;border-radius:4px;font-size:0.92em}
.blog-article-content pre code{background:transparent;color:inherit;padding:0}
.blog-article-content table{width:100%;border-collapse:collapse;margin:22px 0;font-size:14px}
.blog-article-content th,.blog-article-content td{border:1px solid var(--border);padding:10px 12px;text-align:left}
.blog-article-content th{background:var(--cream);color:var(--navy);font-weight:700}
.blog-article-content hr{border:0;border-top:1px solid var(--border);margin:32px 0}
.blog-article-content iframe,.blog-article-content video{max-width:100%;border-radius:var(--radius-sm)}
.blog-article-footer{margin-top:48px;padding-top:32px;border-top:1px solid var(--border);display:flex;flex-direction:column;align-items:stretch;gap:24px;background:transparent}

/* Blog detail */
.blog-detail{background:var(--cream)}
.blog-breadcrumb{padding:20px 0 8px}
.blog-breadcrumb-list{list-style:none;display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin:0;padding:0;font-size:13px;color:var(--text-muted)}
.blog-breadcrumb-list a{color:var(--navy);text-decoration:none;font-weight:600}
.blog-breadcrumb-list a:hover{color:var(--orange)}
.blog-breadcrumb-sep{color:var(--text-muted)}
.blog-detail-hero{padding:12px 0 40px}
.blog-detail-hero-inner{max-width:920px}
.blog-detail-hero-image{margin:0 0 28px;border-radius:var(--radius-lg);overflow:hidden;border:1px solid var(--border);background:white;box-shadow:0 12px 36px rgba(23,45,64,0.08)}
.blog-detail-hero-image img{width:100%;height:auto;display:block;aspect-ratio:1200/630;object-fit:cover}
.blog-detail-hero-copy{text-align:center;max-width:760px;margin:0 auto}
.blog-detail-title{font-size:clamp(30px,4.2vw,46px);font-weight:800;line-height:1.2;margin-bottom:16px;color:var(--navy)}
.blog-detail-meta{display:flex;flex-wrap:wrap;justify-content:center;align-items:center;gap:8px;font-size:14px;color:var(--text-muted)}
.blog-detail-lead{font-size:18px;line-height:1.7;color:var(--text-body);margin:0 0 28px;padding-bottom:22px;border-bottom:1px solid var(--border)}

.blog-share{width:100%;background:transparent;border:0;padding:0;margin:0;box-shadow:none}
.blog-share-heading{font-family:'Sora',sans-serif;font-size:16px;font-weight:700;color:var(--navy);margin:0 0 14px;line-height:1.3}
.blog-share-actions{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;width:100%}
.blog-share-btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;width:100%;min-height:46px;height:46px;padding:0 14px;box-sizing:border-box;border-radius:var(--radius-sm);border:1.5px solid var(--border);background:var(--white);color:var(--navy);text-decoration:none;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:600;line-height:1;white-space:nowrap;transition:border-color .2s ease,background-color .2s ease,color .2s ease,transform .2s ease,box-shadow .2s ease}
.blog-share-btn svg{width:16px;height:16px;flex-shrink:0;display:block}
.blog-share-btn:hover{border-color:var(--navy);background:var(--cream);color:var(--navy);transform:translateY(-1px);box-shadow:0 6px 14px rgba(23,45,64,0.08)}
.blog-share-btn:focus-visible{outline:3px solid rgba(193,98,61,0.3);outline-offset:2px}
.blog-article-actions{display:flex;justify-content:flex-start;width:100%}
.blog-back-btn{min-height:44px}

@media(max-width:900px){
    .blog-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
    .blog-detail-hero{padding-bottom:28px}
    .blog-share-actions{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .blog-share-btn:last-child{grid-column:1 / -1}
}
@media(max-width:640px){
    .blog-grid{grid-template-columns:1fr}
    .blog-card-body{padding:20px 18px 22px}
    .blog-article-footer{margin-top:40px;padding-top:28px;gap:20px}
    .blog-share-heading{font-size:15px;margin-bottom:12px}
    .blog-share-actions{grid-template-columns:1fr;gap:12px}
    .blog-share-btn,.blog-share-btn:last-child{grid-column:auto;height:46px;min-height:46px;font-size:14px}
    .blog-article-actions{width:100%}
    .blog-back-btn{width:100%;justify-content:center}
}
</style>
