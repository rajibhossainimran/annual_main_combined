import React from 'react'
import { createRoot } from 'react-dom/client'

export default function Blank() {
  return (
    <div>Main</div>
  )
}

if(document.getElementById('app')){
  createRoot(document.getElementById('app')).render(<Blank/>)
}

