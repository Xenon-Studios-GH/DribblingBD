import Alpine from "alpinejs";
import persist from "@alpinejs/persist";
import Chart from "chart.js/auto";
import ChartDataLabels from "chartjs-plugin-datalabels";
import Swal from "sweetalert2";
import "./bootstrap";

Chart.register(ChartDataLabels);

Alpine.plugin(persist);

window.Alpine = Alpine;
window.Chart = Chart;
window.Swal = Swal;

Alpine.start();
