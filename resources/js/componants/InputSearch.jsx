import React from 'react'

export default function InputSearch({ data, onSelect }) {
  return (
    <div className='input-search-box'>
      <ul>
        {data.map((val, key) => (
          <li key={key} value={val.value} onClick={() => onSelect(val)}>{val.name}</li>
        ))}
      </ul>
    </div>

  )
}
