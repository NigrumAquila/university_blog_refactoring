function show_hide_password() {
    pas_svg = document.getElementById("svg_password");
    pas_input = document.getElementsByName("password")[0];
    
    pas_svg_href = pas_svg.getAttribute("xlink:href") == "/svg/eye_show.svg" ? "/svg/eye_hide.svg" : "/svg/eye_show.svg";
    pas_input_type = pas_input.getAttribute("type") == "password" ? "text" : "password";
    
    pas_svg.setAttribute("xlink:href", pas_svg_href);
    pas_input.setAttribute("type", pas_input_type)
};