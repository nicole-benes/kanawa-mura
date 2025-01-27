import { test, expect } from '@playwright/test';

test('Skills Accordions', async ({ page }) => {
  // Go to http://km.test/
  await page.goto('http://km.test/');

  // Click the skills link
  await page.getByRole('heading', { name: 'Skills' }).getByRole('link').click();

  // Check if we have the four skills we expect to find
  await expect( page.getByRole('heading', { name: 'Sincerity (Awareness)' })).toBeVisible();
  await expect( page.getByRole('heading', { name: 'Craft (Intelligence, Agility, Awareness)' })).toBeVisible();
  await expect( page.getByRole('heading', { name: 'Battle (Perception)' })).toBeVisible();
  await expect( page.getByRole('heading', { name: 'Stealth (Agility)' })).toBeVisible();

  // Click the high skills accordion
  await page.getByRole('button', { name: 'High Skills' }).click();

  // Check if the Sincerity (Awareness) skill is hidden
  await expect( page.getByRole('heading', { name: 'Sincerity (Awareness)' })).toBeHidden();

  // Click the merchant skills accordion
  await page.getByRole('button', { name: 'Merchant Skills' }).click();

  // Check if the Craft (Intelligence, Agility, Awareness) skill is hidden
  await expect( page.getByRole('heading', { name: 'Craft (Intelligence, Agility, Awareness)' })).toBeHidden();

  // Click the bugei skills accordion
  await page.getByRole('button', { name: 'Bugei Skills' }).click();

  // Check if the Battle (Perception) skill is hidden
  await expect( page.getByRole('heading', { name: 'Battle (Perception)' })).toBeHidden();

  // Click the low skills accordion
  await page.getByRole('button', { name: 'Low Skills' }).click();
  
  // Check if the Stealth (Agility) skill is hidden
  await expect( page.getByRole('heading', { name: 'Stealth (Agility)' })).toBeHidden();

  // Click the merchant skills accordion again
  await page.getByRole('button', { name: 'Merchant Skills' }).click();

  // See if Engineer (Intelligence) is visible
  await expect( page.getByRole('heading', { name: 'Engineering (Intelligence)' })).toBeVisible();

  // Click the high skills accordion again
  await page.getByRole('button', { name: 'High Skills' }).click();

  // Check if Calligraphy (Intelligence) is visible
  await expect( page.getByRole('heading', { name: 'Calligraphy (Intelligence)' })).toBeVisible();

  // Click the low skills accordion again
  await page.getByRole('button', { name: 'Low Skills' }).click();

  // Check if Sleight of Hand (Agility) is visible
  await expect( page.getByRole('heading', { name: 'Sleight of Hand (Agility)' })).toBeVisible();

  // Click the bugei skills accordion again
  await page.getByRole('button', { name: 'Bugei Skills' }).click();

  // Check if Hunting (Perception) is visible
  await expect( page.getByRole('heading', { name: 'Hunting (Perception)' })).toBeVisible();  
});