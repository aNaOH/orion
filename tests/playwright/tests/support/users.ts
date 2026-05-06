export const TEST_PASSWORD = '0R1ON_together';

export const TEST_USERS = {
  admin: {
    email: 'admin@togetheronorion.com',
    password: TEST_PASSWORD,
  },
  reporterForm: {
    id: 100,
    email: 'test.reporter.form@orion.local',
    password: TEST_PASSWORD,
  },
  reporterSelf: {
    id: 101,
    email: 'test.reporter.self@orion.local',
    password: TEST_PASSWORD,
  },
  reporterDuplicate: {
    id: 102,
    email: 'test.reporter.duplicate@orion.local',
    password: TEST_PASSWORD,
  },
  reporterValidation: {
    id: 103,
    email: 'test.reporter.validation@orion.local',
    password: TEST_PASSWORD,
  },
  reporterAdminFlow: {
    id: 104,
    email: 'test.reporter.adminflow@orion.local',
    password: TEST_PASSWORD,
  },
  targetSpam: {
    id: 110,
  },
  targetIdentity: {
    id: 111,
  },
  targetAvatar: {
    id: 112,
  },
} as const;
