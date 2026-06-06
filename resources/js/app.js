import Alpine from "alpinejs";
import persist from "@alpinejs/persist";
import { gsap } from "gsap";
import { Flip } from "gsap/Flip";
import "./bootstrap";

gsap.registerPlugin(Flip);
Alpine.plugin(persist);

window.Alpine = Alpine;
window.gsap = gsap;
window.Flip = Flip;

Alpine.start();
