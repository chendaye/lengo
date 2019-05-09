import defaultSettings from '@/settings'

const title = defaultSettings.title || 'Lengo'

export default function getPageTitle(pageTitle) {
  if (pageTitle) {
    return `${pageTitle} - ${title}`
  }
  return `${title}`
}
