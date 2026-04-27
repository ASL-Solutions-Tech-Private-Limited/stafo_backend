function searchFunction() {
    let input, filter, sidebarLinks, collapsibleSections, i, txtValue, section, sectionLinks;
    input = document.getElementById('searchInput');
    filter = input.value.toLowerCase();
    sidebarLinks = document.querySelectorAll('.sidebar a');
    collapsibleSections = document.querySelectorAll('.collapse');

    // Loop through all sidebar links
    for (i = 0; i < sidebarLinks.length; i++) {
        txtValue = sidebarLinks[i].textContent || sidebarLinks[i].innerText;

        // Check if the link matches the search query
        if (txtValue.toLowerCase().indexOf(filter) > -1) {
            sidebarLinks[i].style.display = "";
        } else {
            sidebarLinks[i].style.display = "none";
        }
    }

    // Loop through all collapsible sections
    for (i = 0; i < collapsibleSections.length; i++) {
        section = collapsibleSections[i];
        sectionLinks = section.querySelectorAll('a');
        let sectionMatches = false;

        // Check if any link inside the section matches the search query
        for (let j = 0; j < sectionLinks.length; j++) {
            txtValue = sectionLinks[j].textContent || sectionLinks[j].innerText;

            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                sectionMatches = true;
                sectionLinks[j].style.display = "";
            } else {
                sectionLinks[j].style.display = "none";
            }
        }

        // If the section matches, ensure it is expanded (shown)
        if (sectionMatches) {
            section.classList.add('show'); // Expands the collapsible section
        } else {
            section.classList.remove('show'); // Collapses the section
        }
    }
}



 document.addEventListener("DOMContentLoaded", function () {
            const sidebar = document.querySelector(".col-md-3");
            const mainContent = document.querySelector(".col-md-9");

            function updateLayout() {
                if (window.innerWidth < 992) {
                    // Hide sidebar and expand main content
                    sidebar?.classList.add("d-none");
                    mainContent.classList.remove("col-md-9");
                    mainContent.classList.add("col-md-12");
                } else {
                    // Show sidebar and reset main content width
                    sidebar?.classList.remove("d-none");
                    mainContent.classList.remove("col-md-12");
                    mainContent.classList.add("col-md-9");
                }
            }

            // Run on page load
            updateLayout();

            // Run on window resize
            window.addEventListener("resize", updateLayout);
        });