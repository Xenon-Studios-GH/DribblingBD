import Alpine from "alpinejs";
import persist from "@alpinejs/persist";
import { gsap } from "gsap";
import { Flip } from "gsap/Flip";
import Chart from "chart.js/auto";
import ChartDataLabels from "chartjs-plugin-datalabels";
import "./bootstrap";

Chart.register(ChartDataLabels);

gsap.registerPlugin(Flip);
Alpine.plugin(persist);

window.Alpine = Alpine;
window.gsap = gsap;
window.Flip = Flip;
window.Chart = Chart;

Alpine.start();
