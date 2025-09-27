import '../css/app.css'
import { createInertiaApp } from '@inertiajs/react'
import { createRoot } from 'react-dom/client'
import { route } from 'ziggy-js'
import LoaderOverlay from '@/Components/LoaderOverlay.jsx'

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true })
    return pages[`./Pages/${name}.jsx`]
  },
  setup({ el, App, props }) {
    createRoot(el).render(
      <>
        <App {...props} />
        <LoaderOverlay />
      </>
    )
  },
})

window.route = (name, params, absolute) => route(name, params, absolute, Ziggy);
