name: 💚 Bug 报告
description: 机械永生,刻不容缓!
labels: ['bug']

body:
- type: markdown
  attributes:
  value: |
  > 注意:如果不是BUG将不会受理



- type: input
  id: description_error
  attributes:
  label: 描述错误
  description: 清楚简洁地说明错误是什么
  placeholder: 由于....
  validations:
  required: true

- type: textarea
  id: step
  attributes:
  label: 重现步骤
  description: 如何重现所述问题步骤，并贴上相关的数据
  placeholder: |
  1.
  2.
  3.
  ...
  render: bash
  validations:
  required: true
- type: markdown
  attributes:
  value: 如果觉得文字无法描述清楚，可以贴上您相关的运行截图，图片请不要使用外链

- type: textarea
  id: environment
  attributes:
  label: 相关环境说明
  description: 详细的日志版本、php版本等
  placeholder: |
  Vpay版本 x.x.x
  PHP版本 x.x.x
  ...
  validations:
  required: true

- type: textarea
  id: logs
  attributes:
  label: 日志内容
  placeholder: |
  2021-11-22 09:30:23,796 - MainProcess[79706] - INFO: Web_ClassCongregation_Plugins(class)_init(def)
  2021-11-22 09:30:23,796 - MainProcess[79706] - WARNING: table Plugins already exists
  2021-11-22 09:30:24,145 - MainProcess[79706] - INFO: Watching for file changes with StatReloader
  ...
  validations:
  required: true


- type: checkboxes
  id: terms
  attributes:
  label: 这不是重复的 issue
  options:
  - label: 我已经搜索了[现有 issue](https://github.com/dreamncn/Qianji_auto/issues)，以确保该错误尚未被报告。
  required: true