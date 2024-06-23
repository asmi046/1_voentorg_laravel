document.addEventListener("DOMContentLoaded", () => {
    let showMobCataloMenuBtn = document.querySelector('.show_mob_menu')
    let mobCatalogMenu = document.querySelector('.mobile_catalog_menu')

    let mobCatalogMenuList = document.querySelectorAll('.catalogMenuPunctTitle')

    if (mobCatalogMenuList)
        for (let item of mobCatalogMenuList)
        item.onclick = (e) =>  {
            e.preventDefault()
            e.stopPropagation()

            item.parentNode.classList.toggle("_active")
        }

    if (showMobCataloMenuBtn)
        showMobCataloMenuBtn.onclick = (e) =>  {
            e.preventDefault()
            showMobCataloMenuBtn.classList.toggle("menu_open")

            let btnElement = showMobCataloMenuBtn.querySelector(".icon");

            btnElement.classList.remove("icon-ec_icon_abort")
            btnElement.classList.remove("icon-ec_icon_menu")

            if (showMobCataloMenuBtn.classList.contains("menu_open"))
                btnElement.classList.add("icon-ec_icon_abort")
            else
                btnElement.classList.add("icon-ec_icon_menu")

            mobCatalogMenu.classList.toggle("mcm_active")
        }

})
