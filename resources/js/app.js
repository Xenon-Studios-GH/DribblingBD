import Alpine from "alpinejs";
import persist from "@alpinejs/persist";
import "./bootstrap";

Alpine.plugin(persist);

window.Alpine = Alpine;
Alpine.start();
