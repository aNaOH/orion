const searchForm = document.getElementById("searchForm");
const searchInput = document.getElementById("search");
const filterForm = document.getElementById("filterForm");

function applyFilter(page = 1) {
  const formData = new FormData(filterForm);
  if (searchInput.value.trim() !== "") {
    formData.append("search", searchInput.value);
  }
  if (formData.get("genre") == "all") {
    formData.delete("genre");
  }
  if (page > 1) {
    formData.append("page", page);
  }
  const params = new URLSearchParams();

  // Pasamos todos los valores del form
  for (const [key, value] of formData.entries()) {
    if (key === "features[]") {
      // Acumulamos features en un array
      if (!params.has("features")) {
        params.set("features", value);
      } else {
        params.set("features", params.get("features") + "," + value);
      }
    } else {
      params.append(key, value);
    }
  }
  const paramsString = params.toString();
  if (paramsString.trim().length > 0) {
    window.location.href = `/communities?${params.toString()}`;
  } else {
    window.location.href = `/communities`;
  }
}

if (searchForm) {
  searchForm.addEventListener("submit", (event) => {
    event.preventDefault();
    applyFilter();
  });
}

if (filterForm) {
  filterForm.addEventListener("submit", (event) => {
    event.preventDefault();
    applyFilter();
  });
}

const previousBtn = document.getElementById("prevPage");
const nextBtn = document.getElementById("nextPage");

if (previousBtn) {
  previousBtn.addEventListener("click", () => {
    const match = window.location.search.match(/page=(\d+)/);
    const currentPage = match ? parseInt(match[1]) : 1;
    applyFilter(currentPage - 1);
  });
}

if (nextBtn) {
  nextBtn.addEventListener("click", () => {
    const match = window.location.search.match(/page=(\d+)/);
    const currentPage = match ? parseInt(match[1]) : 1;
    applyFilter(currentPage + 1);
  });
}

// Mobile Filter Logic
const openMobileBtn = document.getElementById("openMobileFilters");
const closeMobileBtn = document.getElementById("closeMobileFilters");
const sidebar = document.getElementById("sidebarFilters");
const filterFormInSidebar = document.querySelector("#sidebarFilters form");
const backdrop = document.getElementById("mobileFiltersBackdrop");

function toggleMobileFilters(isOpen) {
  if (isOpen) {
    sidebar.classList.remove("invisible", "opacity-0");
    sidebar.classList.add("visible", "opacity-100");
    filterFormInSidebar.classList.remove("-translate-x-full");
    document.body.style.overflow = "hidden"; // Prevent scrolling
  } else {
    sidebar.classList.remove("visible", "opacity-100");
    sidebar.classList.add("invisible", "opacity-0");
    filterFormInSidebar.classList.add("-translate-x-full");
    document.body.style.overflow = ""; // Restore scrolling
  }
}

if (openMobileBtn) {
  openMobileBtn.addEventListener("click", () => toggleMobileFilters(true));
}

if (closeMobileBtn) {
  closeMobileBtn.addEventListener("click", () => toggleMobileFilters(false));
}

if (backdrop) {
  backdrop.addEventListener("click", () => toggleMobileFilters(false));
}
