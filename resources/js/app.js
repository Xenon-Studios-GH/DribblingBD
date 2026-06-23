import Alpine from "alpinejs";
import persist from "@alpinejs/persist";
import collapse from "@alpinejs/collapse";
import Chart from "chart.js/auto";
import ChartDataLabels from "chartjs-plugin-datalabels";
import Swal from "sweetalert2";
import "./bootstrap";
import "./polling-manager";

Chart.register(ChartDataLabels);

Alpine.plugin(persist);
Alpine.plugin(collapse);

window.Alpine = Alpine;
window.Chart = Chart;
window.Swal = Swal;

Alpine.start();
